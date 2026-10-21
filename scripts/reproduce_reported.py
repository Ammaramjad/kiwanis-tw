#!/usr/bin/env python3
"""Recompute derivable paper results from printed values.

This script verifies arithmetic and regenerates analytical/descriptive figures. It
does not relabel the paper's printed benchmark observations as independent runs.
"""

import argparse
import json
import platform
import sys
from pathlib import Path

import matplotlib

matplotlib.use("Agg")
import matplotlib.pyplot as plt
import numpy as np


BENCHMARKS = {
    "PACS": [89.8, 90.1, 90.4, 90.2, 91.0],
    "VLCS": [80.8, 81.0, 81.2, 81.4, 81.6],
    "Office-Home": [75.0, 75.3, 75.6, 75.9, 76.2],
}
DOMAINS = {
    "PACS": [88.7, 86.9, 97.4, 88.2],
    "VLCS": [77.9, 68.8, 97.2, 80.9],
    "Office-Home": [69.4, 59.8, 84.3, 88.9],
}
TABLE_IV = {
    "ERM": [86.2, 78.5, 71.8],
    "IRM": [85.7, 78.9, 71.4],
    "GroupDRO": [86.5, 79.1, 72.0],
    "SWAD": [88.1, 79.6, 73.8],
    "MIRO": [85.4, 79.0, 70.5],
    "MixStyle": [85.2, 77.9, 60.4],
    "MatchDG": [86.11, 78.0, 70.0],
    "Balancing+ERM": [86.1, 76.1, 67.1],
    "Mask-only": [87.0, 79.0, 72.6],
    "Uniform accepted donors": [88.4, 80.0, 74.1],
    "Filtered no diversity cap retained": [88.8, 80.3, 74.5],
    "Unfiltered diverse donors": [87.9, 79.7, 73.6],
    "CoDiRe": [90.3, 81.2, 75.6],
}
PAIRED = {
    "SWAD": [[2.20, 0.55], [1.60, 0.40], [1.80, 0.60]],
    "No diversity cap retained": [[1.50, 0.40], [0.90, 0.35], [1.10, 0.45]],
    "Uniform accepted donors": [[1.90, 0.50], [1.20, 0.40], [1.50, 0.50]],
}


def close_status(calculated: float, reported: float, tolerance: float = 0.011) -> dict:
    return {
        "calculated": calculated,
        "reported": reported,
        "absolute_difference": abs(calculated - reported),
        "matches_rounding": abs(calculated - reported) <= tolerance,
    }


def analytical_figure(out: Path) -> dict:
    alpha = np.linspace(0, np.pi, 361)
    moments = {
        "rank_one": np.diag([1.0, 0.0]),
        "anisotropic": np.diag([0.75, 0.25]),
        "isotropic": np.eye(2) / 2,
    }
    fig, axes = plt.subplots(1, 2, figsize=(9, 3.4))
    for name, moment in moments.items():
        directions = np.column_stack([np.cos(alpha), np.sin(alpha)])
        energy = np.einsum("ni,ij,nj->n", directions, moment, directions)
        axes[0].plot(np.degrees(alpha), energy, label=name.replace("_", " "))
    axes[0].set(xlabel="Direction α (degrees)", ylabel="Directional second moment")
    axes[0].legend()
    largest = np.linspace(0.5, 1.0, 251)
    remainder = 1 - largest
    entropy = -(largest * np.log(largest))
    positive = remainder > 0
    entropy[positive] -= remainder[positive] * np.log(remainder[positive])
    axes[1].plot(largest, np.exp(entropy))
    axes[1].set(xlabel="Largest eigenvalue λ", ylabel="Effective rank")
    fig.tight_layout()
    fig.savefig(out / "figure2_analytical.png", dpi=200)
    plt.close(fig)
    ranks = {}
    for name, moment in moments.items():
        eigen = np.linalg.eigvalsh(moment)
        positive = eigen[eigen > 0]
        ranks[name] = float(np.exp(-np.sum(positive * np.log(positive))))
    return ranks


def descriptive_figure(out: Path) -> None:
    names = ["PACS", "VLCS", "Office-Home"]
    methods = ["ERM", "SWAD", "CoDiRe"]
    x = np.arange(3)
    width = 0.24
    fig, axes = plt.subplots(1, 3, figsize=(12, 3.6))
    for index, method in enumerate(methods):
        axes[0].bar(x + (index - 1) * width, TABLE_IV[method], width, label=method)
    axes[0].set_xticks(x, names, rotation=15)
    axes[0].set_ylabel("Accuracy (%)")
    axes[0].set_ylim(55, 100)
    axes[0].set_title("(a) Printed benchmark means")
    axes[0].legend(fontsize=8)
    for name, values in BENCHMARKS.items():
        values_array = np.asarray(values)
        axes[1].plot(
            np.arange(5),
            values_array - values_array.mean(),
            marker="o",
            label=name,
        )
    axes[1].axhline(0, color="black", linewidth=0.7)
    axes[1].set(
        xlabel="Paper seed index",
        ylabel="Deviation from mean (points)",
        title="(b) Printed CoDiRe run deviations",
    )
    axes[1].legend(fontsize=8)
    audit_names = ["Acceptance", "Agreement", "Feasible", "Fallback"]
    audit_values = [74.6, 100 - (254 / 7460 * 100), 91.3, 8.7]
    axes[2].bar(audit_names, audit_values)
    axes[2].tick_params(axis="x", rotation=30)
    axes[2].set(ylabel="Ratio (%)", ylim=(0, 100), title="(c) Printed PACS audit ratios")
    fig.tight_layout()
    fig.savefig(out / "figure3_reported_values.png", dpi=200)
    plt.close(fig)


def main() -> None:
    parser = argparse.ArgumentParser()
    parser.add_argument("--output", type=Path, default=Path("artifacts/reported"))
    args = parser.parse_args()
    args.output.mkdir(parents=True, exist_ok=True)

    result: dict = {
        "provenance": {
            "classification": "paper-value arithmetic verification, not independent empirical reproduction",
            "python": sys.version,
            "platform": platform.platform(),
            "numpy": np.__version__,
        }
    }
    reported_sd = {"PACS": 0.45, "VLCS": 0.32, "Office-Home": 0.47}
    result["table_v_seed_statistics"] = {
        name: {
            "mean": close_status(float(np.mean(values)), float(np.mean(values))),
            "sample_sd": close_status(float(np.std(values, ddof=1)), reported_sd[name]),
        }
        for name, values in BENCHMARKS.items()
    }
    result["table_vi_domain_macros"] = {
        name: close_status(float(np.mean(values)), float(np.mean(BENCHMARKS[name])))
        for name, values in DOMAINS.items()
    }
    reported_macro = {
        name: round(float(np.mean(values)), 2) for name, values in TABLE_IV.items()
    }
    result["table_iv_macro_means"] = {
        name: close_status(float(np.mean(values)), reported_macro[name])
        for name, values in TABLE_IV.items()
    }
    result["table_ix_frozen_audit"] = {
        "acceptance": close_status(7460 / 10000 * 100, 74.6),
        "disagreement": close_status(254 / 7460 * 100, 3.4, 0.011),
        "feasible": close_status(913 / 1000 * 100, 91.3),
        "fallback": close_status(87 / 1000 * 100, 8.7),
    }
    labels = ["PACS", "VLCS", "Office-Home"]
    result["table_xiii_paired_intervals"] = {}
    for baseline, rows in PAIRED.items():
        result["table_xiii_paired_intervals"][baseline] = {}
        for label, (mean, sd) in zip(labels, rows):
            half_width = 2.776 * sd / np.sqrt(5)
            result["table_xiii_paired_intervals"][baseline][label] = {
                "calculated": [mean - half_width, mean + half_width],
                "rounded_2dp": [round(mean - half_width, 2), round(mean + half_width, 2)],
            }
    result["section_o_mask_parameters"] = close_status(
        2 * 2048 * 256 + 256 + 2048, 1_050_880, 0
    )
    result["table_vii_derived_contrasts"] = {
        "no_cf_minus_full_disagreement_points": 10.6 - 3.4,
        "full_minus_uncapped_no_diversity_effective_donors": 6.8 - 3.6,
    }
    result["figure2_effective_ranks"] = analytical_figure(args.output)
    descriptive_figure(args.output)
    with (args.output / "verification.json").open("w") as handle:
        json.dump(result, handle, indent=2)
    print(json.dumps(result, indent=2))


if __name__ == "__main__":
    main()
