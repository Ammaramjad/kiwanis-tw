"""Constrained donor policy from Eq. (6), solved with SLSQP."""

from dataclasses import dataclass

import numpy as np
from scipy.optimize import minimize


@dataclass(frozen=True)
class PolicyResult:
    weights: np.ndarray
    feasible: bool
    used_fallback: bool
    status: str
    objective: float | None
    cap_residual: float
    normalization_residual: float
    kl_residual: float


def _kl_uniform(q: np.ndarray) -> float:
    positive = q > 0
    k = len(q)
    return float(np.sum(q[positive] * np.log(q[positive] * k)))


def solve_donor_policy(
    difficulty: np.ndarray,
    displacements: np.ndarray,
    *,
    beta: float = 0.1,
    ridge: float = 1e-3,
    qmax: float = 0.2,
    kl_radius: float = 0.2,
    max_iterations: int = 100,
    tolerance: float = 1e-6,
) -> PolicyResult:
    """Maximize difficulty plus log-volume under cap and KL constraints.

    Inputs must already be compatibility-filtered and standardized/projected.
    Candidate statistics are treated as detached constants, as required by the paper.
    """
    difficulty = np.asarray(difficulty, dtype=float)
    z = np.asarray(displacements, dtype=float)
    if difficulty.ndim != 1 or z.ndim != 2 or len(difficulty) != len(z):
        raise ValueError("difficulty must be (k,) and displacements must be (k,r)")
    k = len(difficulty)
    if k == 0:
        return PolicyResult(
            np.empty(0), False, True, "empty accepted pool", None, 0.0, 1.0, 0.0
        )
    if k * qmax < 1.0 - tolerance:
        return PolicyResult(
            np.zeros(k),
            False,
            True,
            f"infeasible cap: {k} * {qmax} < 1",
            None,
            1.0 - k * qmax,
            1.0,
            0.0,
        )

    uniform = np.full(k, 1.0 / k)
    eye = np.eye(z.shape[1])

    def objective(q: np.ndarray) -> float:
        moment = np.einsum("k,ki,kj->ij", q, z, z)
        sign, logdet = np.linalg.slogdet(moment + ridge * eye)
        if sign <= 0:
            return np.inf
        return -float(q @ difficulty + beta * logdet)

    constraints = [
        {"type": "eq", "fun": lambda q: np.sum(q) - 1.0},
        {"type": "ineq", "fun": lambda q: kl_radius - _kl_uniform(q)},
    ]
    result = minimize(
        objective,
        uniform,
        method="SLSQP",
        bounds=[(0.0, qmax)] * k,
        constraints=constraints,
        options={"maxiter": max_iterations, "ftol": tolerance},
    )
    q = result.x if result.success else uniform
    used_fallback = not result.success
    cap_residual = max(0.0, float(np.max(q) - qmax))
    normalization_residual = abs(float(np.sum(q) - 1.0))
    kl_residual = max(0.0, _kl_uniform(q) - kl_radius)
    valid = max(cap_residual, normalization_residual, kl_residual) <= 10 * tolerance
    if not valid:
        q = uniform
        used_fallback = True
        cap_residual = max(0.0, float(np.max(q) - qmax))
        normalization_residual = abs(float(np.sum(q) - 1.0))
        kl_residual = max(0.0, _kl_uniform(q) - kl_radius)
    return PolicyResult(
        q,
        True,
        used_fallback,
        result.message if not used_fallback else f"uniform fallback: {result.message}",
        -objective(q),
        cap_residual,
        normalization_residual,
        kl_residual,
    )
