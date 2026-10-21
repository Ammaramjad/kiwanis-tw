# CoDiRe paper verification harness

This repository contains a transparent reconstruction of the uploaded
“Compatibility-Constrained Feature Recombination for Domain-General Representation
Learning” manuscript.

```bash
python3 -m pip install -e '.[test]'
python3 scripts/reproduce_reported.py
python3 scripts/smoke_feature_pipeline.py --seed 42
pytest -q
```

Read [REPRODUCTION.md](REPRODUCTION.md) first. It distinguishes independent
analytical/method checks from arithmetic performed on paper-reported values and
explains why the claimed image benchmark training cannot be independently reproduced
from the supplied repository.

Generated artifacts:

- `artifacts/reported/verification.json`: recomputed printed-value arithmetic
- `artifacts/reported/figure2_analytical.png`: independent analytical Figure 2
- `artifacts/reported/figure3_reported_values.png`: all descriptive Figure 3 panels
- `artifacts/smoke.json`: deterministic synthetic method execution
