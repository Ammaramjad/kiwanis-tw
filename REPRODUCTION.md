# CoDiRe reproduction record

## Evidence classification

The uploaded manuscript is anonymous and gives no code, checkpoint, dataset
manifest, per-image predictions, or implementation URL. Public searches for the
exact title and exact benchmark score tuple found no matching release. A different
2026 paper also calls an unrelated continual-test-time-adaptation method “CoDiRe.”
The manuscript's disclosure says AI tools helped create the plots and manuscript
but “did not execute the training experiments.” Consequently, the printed empirical
values are **not independently reproducible from the supplied materials**.

This repository deliberately separates:

1. **Arithmetic verification**: calculations made from values printed in the paper.
2. **Analytical reproduction**: equations/curves whose inputs are fully specified.
3. **Method smoke execution**: deterministic synthetic features exercising the
   shared prediction path, compatibility gate, SLSQP donor policy, and audit.
4. **Benchmark reproduction**: blocked; no datasets, run manifests, checkpoints,
   GPU, or original implementation are available.

Artifacts always carry one of these classifications. A matching recomputation from
printed values is not evidence that a training run occurred.

## Method reconstructed

For backbone feature `u`, a two-layer input-dependent sigmoid mask forms retained
`c(u)=m(u)⊙u` and replaceable `s(u)=(1-m(u))⊙u` branches. Candidate
`v_ij=c(u_i)+s(u_j)` is passed through the *same full mask and classifier* as used
at inference. A frozen MLP accepts same-class donors when label confidence exceeds
`γ` and hidden-feature distance is below `r_b`.

For accepted candidates, standardized replaceable-branch displacements are projected
onto a frozen 16-dimensional PCA basis. SLSQP maximizes detached normalized
predictive KL plus `β log det(G+εI)`, subject to a simplex, `q_j≤0.2`, and
`KL(q||uniform)≤0.2`. Fewer than five accepted donors is infeasible. Training
combines factual cross entropy, policy-weighted candidate consistency, mask-budget,
and mask-binarization losses. Infeasible anchors retain supervised loss; frozen
audit uses identity fallback.

The feature implementation covers Eqs. (2)–(6), policy constraints, geometry, and
Eqs. (21)–(24) audit denominators. It does not claim to replace the missing
ResNet-50 training implementation.

## Complete reported-result inventory

| Item | Reported content | Status here |
|---|---|---|
| Table I | notation | transcribed methodology; no numerical experiment |
| Table II | baseline search space, 20 trials, seeds 0–4, 10k steps | documented; search not executable |
| Table III | full CoDiRe configuration | implemented at feature/policy level; image training blocked |
| Table IV | 13 methods × 3 benchmarks, mean±SD and macro | all macro arithmetic verified from printed means |
| Table V | five CoDiRe seed outcomes | means and sample SDs exactly recomputed |
| Table VI | 12 held-out-domain means±SD | benchmark domain macros recomputed |
| Table VII | seven component audits | printed values inventoried; two stated contrasts recomputed |
| Table VIII | pathway/geometry diagnostics | estimands implemented; reported observations unavailable |
| Table IX | PACS counts/ratios | all four ratios recomputed |
| Table X | seven-point compatibility sweep | printed observations inventoried; no checkpoints |
| Table XI | five donor policies | estimands/constraints implemented; no frozen candidate pools |
| Table XII | four mask temperatures | printed observations inventoried; no checkpoints |
| Table XIII | nine paired gains and 95% t intervals | all intervals recomputed using stated formula |
| Table XIV | published contextual means | source-paper values, not this study's reruns |
| Table XV | qualitative evidence map | no numerical result |
| Figure 1 | architecture | supplied repository artwork; equations implemented |
| Figure 2 | analytical directional curves/effective rank | independently regenerated |
| Figure 3 | descriptive plot of Tables IV/V/IX | benchmark panel regenerated from printed data only |
| Eqs. 14–18 | conditional bounds | algebraic claims; cannot instantiate unknown κ,ρ,ξ,η |
| Eq. 19–20 | directional geometry/effective rank | implemented and smoke-tested |
| Section O | mask parameter count 1,050,880 | exactly recomputed |

Additional printed empirical claims inventoried:

- Main CoDiRe: PACS `90.30±0.45`, VLCS `81.20±0.32`,
  Office-Home `75.60±0.47`, macro `82.37`.
- Table VII full audit: acceptance `74.6±2.1%`, disagreement `3.4±0.5%`,
  effective donors `6.8±0.4`; all other ablation rows remain unverified observations.
- Table VIII full/no-diversity `(D_c,V_m,sensitivity,near-zero,tr(G),r_eff)`:
  `(0.080,0.012,0.040,2.0%,6.20,8.17)` and
  `(0.075,0.011,0.055,3.0%,6.10,5.80)`.
- Table X `(γ,accept,disagree,macro)`:
  `(0.3,94.2,8.9,80.6)`, `(0.4,87.5,6.7,81.3)`,
  `(0.5,80.9,4.9,81.9)`, `(0.6,74.6,3.4,82.37)`,
  `(0.7,62.8,2.9,82.0)`, `(0.8,48.7,2.5,81.4)`,
  `(0.9,29.5,2.2,80.7)`.
- Table XI `(accept,effective donors,largest weight)`:
  uniform all `(100,9.8,0.10)`, compatible uniform `(74.9,4.9,0.24)`,
  difficulty only `(74.7,4.2,0.29)`, diversity only `(75.2,7.1,0.18)`,
  full `(74.6,6.8,0.19)`.
- Table XII `(T_m,active,disagree,macro)`:
  `(2,58.2,5.2,81.4)`, `(1,51.4,4.1,82.0)`,
  `(0.5,46.8,3.4,82.37)`, `(0.25,41.7,3.2,81.8)`.

## What was run and concrete results

Run:

```bash
python3 -m pip install -e '.[test]'
python3 scripts/reproduce_reported.py
python3 scripts/smoke_feature_pipeline.py --seed 42
pytest -q
```

Machine-readable results are under `artifacts/`. Important expected checks:

- Table V sample SDs recompute to `0.4472`, `0.3162`, `0.4743`, which round
  to the paper's `0.45`, `0.32`, `0.47`.
- Table VI domain means recompute to `90.30`, `81.20`, `75.60`.
- Table IX ratios recompute to `74.6%`, `3.4048%` (paper `3.4%`),
  `91.3%`, and `8.7%`.
- Analytical effective ranks are `1`, `1.754765…`, and `2`.
- The mask parameter formula gives `1,050,880`.

Smoke output numbers are implementation checks only and must not be compared to the
paper's image benchmarks.

## Full benchmark execution requirements and blockers

The claimed evaluation implies 3 datasets × 4 held-out targets × 5 seeds, plus
20 source-validation trials for each method/task and CoDiRe ablations/sweeps.
Faithful execution needs:

- licensed/downloaded PACS, VLCS, and Office-Home images and exact split manifests;
- ImageNet ResNet-50 weights and the unspecified code that maps the protocol to
  DomainBed splits/preprocessing;
- original per-trial hyperparameter draws, duplicate hashes, donor-bank memberships,
  validation/audit partitions, selected checkpoint steps, and implementation commit;
- trained teacher/student checkpoints or enough GPU compute for hundreds of
  10,000-step runs;
- underlying paired run outcomes for Table XIII (marginal summaries cannot recover
  covariance).

None is present. This host has no visible NVIDIA device and initially had no PyTorch.
Downloading public benchmark data would still not recover the undisclosed splits,
run records, or original implementation, so expensive training here would be a new
study rather than a faithful reproduction. Semantic mismatch `ξ` and target support
`η` are explicitly unmeasured in the paper and cannot be reconstructed.
