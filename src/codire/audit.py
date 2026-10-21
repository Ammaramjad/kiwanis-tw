"""Frozen-audit estimands from Sections V-C and V-G."""

import numpy as np


def effective_rank(moment: np.ndarray) -> float:
    eigenvalues = np.maximum(np.linalg.eigvalsh(moment), 0.0)
    trace = float(eigenvalues.sum())
    if trace <= 0:
        return 0.0
    probabilities = eigenvalues[eigenvalues > 0] / trace
    return float(np.exp(-np.sum(probabilities * np.log(probabilities))))


def geometry_metrics(z: np.ndarray, weights: np.ndarray) -> dict[str, float]:
    moment = np.einsum("k,ki,kj->ij", weights, z, z)
    return {
        "trace": float(np.trace(moment)),
        "effective_rank": effective_rank(moment),
    }


def audit_candidates(
    accepted: np.ndarray,
    anchor_predictions: np.ndarray,
    candidate_predictions: np.ndarray,
    feasible_anchors: np.ndarray,
) -> dict[str, float | int | None]:
    """Compute paper denominators exactly; undefined empty ratios become None."""
    accepted = np.asarray(accepted, dtype=bool)
    factual_class = np.argmax(anchor_predictions, axis=1)
    candidate_class = np.argmax(candidate_predictions, axis=1)
    attempts = int(len(accepted))
    accepted_count = int(accepted.sum())
    anchors = int(len(feasible_anchors))
    feasible = int(np.asarray(feasible_anchors, dtype=bool).sum())
    disagreement = int(np.sum((factual_class != candidate_class) & accepted))
    return {
        "attempted_candidates": attempts,
        "accepted_candidates": accepted_count,
        "acceptance": accepted_count / attempts if attempts else None,
        "prediction_disagreements": disagreement,
        "conditional_disagreement": (
            disagreement / accepted_count if accepted_count else None
        ),
        "anchors": anchors,
        "feasible_anchors": feasible,
        "feasible_rate": feasible / anchors if anchors else None,
        "identity_fallback_anchors": anchors - feasible,
        "identity_fallback_rate": (anchors - feasible) / anchors if anchors else None,
    }
