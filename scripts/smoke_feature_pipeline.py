#!/usr/bin/env python3
"""Deterministic synthetic execution of construction, policy, and audit."""

import argparse
import json
import platform
import time
from pathlib import Path

import numpy as np

from codire.audit import audit_candidates, geometry_metrics
from codire.core import CoDiReModel, FrozenCompatibilityTeacher, kl_divergence
from codire.policy import solve_donor_policy


def main() -> None:
    parser = argparse.ArgumentParser()
    parser.add_argument("--seed", type=int, default=42)
    parser.add_argument("--anchors", type=int, default=24)
    parser.add_argument("--donors", type=int, default=10)
    parser.add_argument("--dimension", type=int, default=32)
    parser.add_argument("--output", type=Path, default=Path("artifacts/smoke.json"))
    args = parser.parse_args()
    start = time.perf_counter()
    rng = np.random.default_rng(args.seed)
    n, k, d, hidden, classes, projection = (
        args.anchors,
        args.donors,
        args.dimension,
        12,
        4,
        8,
    )
    anchors = rng.normal(size=(n, d))
    donor_bank = anchors[:, None, :] + rng.normal(0, 0.65, size=(n, k, d))
    model = CoDiReModel(
        rng.normal(0, 0.15, (d, hidden)),
        rng.normal(0, 0.05, hidden),
        rng.normal(0, 0.15, (hidden, d)),
        rng.normal(0, 0.05, d),
        rng.normal(0, 0.2, (d, classes)),
        rng.normal(0, 0.05, classes),
    )
    teacher = FrozenCompatibilityTeacher(
        rng.normal(0, 0.2, (d, hidden)),
        rng.normal(0, 0.05, hidden),
        rng.normal(0, 0.2, (hidden, classes)),
        rng.normal(0, 0.05, classes),
    )
    labels = np.argmax(teacher.predict(anchors), axis=1)
    repeated_anchors = np.repeat(anchors, k, axis=0)
    donors = donor_bank.reshape(n * k, d)
    candidates = model.recombine(repeated_anchors, donors)
    factual = model.predict(repeated_anchors)
    candidate = model.predict(candidates)
    teacher_distances = np.linalg.norm(
        teacher.embed(candidates) - teacher.embed(repeated_anchors), axis=1
    )
    # A data-derived source-only diagnostic threshold keeps the smoke nonempty.
    rb = float(np.quantile(teacher_distances, 0.9))
    accepted = teacher.accepted(
        candidates,
        repeated_anchors,
        np.repeat(labels, k),
        gamma=0.15,
        distance_threshold=rb,
    ).reshape(n, k)

    projection_matrix, _ = np.linalg.qr(rng.normal(size=(d, projection)))
    feasible = np.zeros(n, dtype=bool)
    effective_donors = []
    geometries = []
    solver_fallbacks = 0
    max_residual = 0.0
    for index in range(n):
        chosen = np.flatnonzero(accepted[index])
        if not len(chosen):
            continue
        donor_features = donor_bank[index, chosen]
        anchor_batch = np.repeat(anchors[index][None, :], len(chosen), axis=0)
        _, anchor_s = model.partition(anchor_batch)
        _, donor_s = model.partition(donor_features)
        z = (donor_s - anchor_s) @ projection_matrix
        divergence = kl_divergence(
            model.predict(anchor_batch), model.candidate_predict(anchor_batch, donor_features)
        )
        scaling = max(float(np.mean(divergence)), 1e-6)
        policy = solve_donor_policy(divergence / scaling, z)
        feasible[index] = policy.feasible
        if not policy.feasible:
            continue
        solver_fallbacks += int(policy.used_fallback)
        max_residual = max(
            max_residual,
            policy.cap_residual,
            policy.normalization_residual,
            policy.kl_residual,
        )
        effective_donors.append(float(1 / np.sum(policy.weights**2)))
        geometries.append(geometry_metrics(z, policy.weights))

    audit = audit_candidates(
        accepted.ravel(), factual, candidate, feasible
    )
    bypass_error = float(
        np.max(np.abs(model.bypass_predict(repeated_anchors, donors) - factual))
    )
    self_error = float(
        np.max(np.abs(model.recombine(anchors, anchors) - anchors))
    )
    output = {
        "classification": "synthetic smoke execution; not a benchmark reproduction",
        "seed": args.seed,
        "environment": {
            "platform": platform.platform(),
            "numpy": np.__version__,
        },
        "configuration": vars(args) | {"output": str(args.output)},
        "audit": audit,
        "feasible_policy_mean_effective_donors": (
            float(np.mean(effective_donors)) if effective_donors else None
        ),
        "mean_geometry": {
            key: float(np.mean([item[key] for item in geometries]))
            for key in ("trace", "effective_rank")
        } if geometries else None,
        "solver_uniform_fallbacks": solver_fallbacks,
        "maximum_constraint_residual": max_residual,
        "identity_checks": {
            "self_recombination_max_abs_error": self_error,
            "exact_bypass_max_abs_error": bypass_error,
        },
        "runtime_seconds": time.perf_counter() - start,
    }
    args.output.parent.mkdir(parents=True, exist_ok=True)
    with args.output.open("w") as handle:
        json.dump(output, handle, indent=2)
    print(json.dumps(output, indent=2))


if __name__ == "__main__":
    main()
