"""Feature-level CoDiRe reference implementation."""

from .audit import audit_candidates, effective_rank, geometry_metrics
from .core import CoDiReModel, FrozenCompatibilityTeacher
from .policy import PolicyResult, solve_donor_policy

__all__ = [
    "CoDiReModel",
    "FrozenCompatibilityTeacher",
    "PolicyResult",
    "audit_candidates",
    "effective_rank",
    "geometry_metrics",
    "solve_donor_policy",
]
