import numpy as np

from codire.audit import audit_candidates, effective_rank
from codire.core import CoDiReModel
from codire.policy import solve_donor_policy


def model(seed: int = 1) -> CoDiReModel:
    rng = np.random.default_rng(seed)
    d, h, c = 6, 3, 2
    return CoDiReModel(
        rng.normal(size=(d, h)),
        rng.normal(size=h),
        rng.normal(size=(h, d)),
        rng.normal(size=d),
        rng.normal(size=(d, c)),
        rng.normal(size=c),
    )


def test_self_recombination_and_bypass_identity():
    rng = np.random.default_rng(2)
    features = rng.normal(size=(5, 6))
    endpoint = model()
    np.testing.assert_allclose(endpoint.recombine(features, features), features)
    np.testing.assert_allclose(
        endpoint.bypass_predict(features, features[::-1]), endpoint.predict(features)
    )


def test_policy_constraints_and_cap_feasibility():
    rng = np.random.default_rng(3)
    result = solve_donor_policy(rng.random(7), rng.normal(size=(7, 3)))
    assert result.feasible
    np.testing.assert_allclose(result.weights.sum(), 1.0, atol=1e-6)
    assert result.weights.max() <= 0.2 + 1e-6
    assert 1 / np.sum(result.weights**2) >= 5 - 1e-5
    infeasible = solve_donor_policy(rng.random(4), rng.normal(size=(4, 3)))
    assert not infeasible.feasible
    assert infeasible.used_fallback


def test_audit_denominators_and_effective_rank():
    accepted = np.array([True, True, False])
    factual = np.array([[0.8, 0.2], [0.8, 0.2], [0.1, 0.9]])
    candidates = np.array([[0.7, 0.3], [0.2, 0.8], [0.9, 0.1]])
    result = audit_candidates(accepted, factual, candidates, np.array([True, False]))
    assert result["acceptance"] == 2 / 3
    assert result["conditional_disagreement"] == 1 / 2
    assert result["identity_fallback_rate"] == 1 / 2
    np.testing.assert_allclose(
        effective_rank(np.diag([0.75, 0.25])), 1.7547653506, rtol=1e-9
    )
