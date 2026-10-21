"""NumPy implementation of the paper's shared candidate prediction path.

This module intentionally operates on backbone features. It implements Eqs. (2)--(4)
without pretending that a synthetic feature smoke test reproduces image benchmarks.
"""

from dataclasses import dataclass

import numpy as np


def _sigmoid(x: np.ndarray) -> np.ndarray:
    positive = x >= 0
    out = np.empty_like(x, dtype=float)
    out[positive] = 1.0 / (1.0 + np.exp(-x[positive]))
    exp_x = np.exp(x[~positive])
    out[~positive] = exp_x / (1.0 + exp_x)
    return out


def _softmax(x: np.ndarray) -> np.ndarray:
    shifted = x - np.max(x, axis=-1, keepdims=True)
    exp = np.exp(shifted)
    return exp / exp.sum(axis=-1, keepdims=True)


@dataclass(frozen=True)
class CoDiReModel:
    """Frozen mask/classifier endpoint used by factual and candidate paths."""

    mask_w1: np.ndarray
    mask_b1: np.ndarray
    mask_w2: np.ndarray
    mask_b2: np.ndarray
    classifier_w: np.ndarray
    classifier_b: np.ndarray
    temperature: float = 0.5
    probability_floor: float = 1e-6

    def mask(self, features: np.ndarray) -> np.ndarray:
        hidden = np.maximum(features @ self.mask_w1 + self.mask_b1, 0.0)
        return _sigmoid((hidden @ self.mask_w2 + self.mask_b2) / self.temperature)

    def partition(self, features: np.ndarray) -> tuple[np.ndarray, np.ndarray]:
        mask = self.mask(features)
        return mask * features, (1.0 - mask) * features

    def predict(self, features: np.ndarray) -> np.ndarray:
        retained, _ = self.partition(features)
        probs = _softmax(retained @ self.classifier_w + self.classifier_b)
        classes = probs.shape[-1]
        tau = self.probability_floor
        if not 0.0 < tau < 1.0 / classes:
            raise ValueError("probability_floor must lie in (0, 1/C)")
        return (1.0 - classes * tau) * probs + tau

    def recombine(self, anchors: np.ndarray, donors: np.ndarray) -> np.ndarray:
        retained, _ = self.partition(anchors)
        _, replaced = self.partition(donors)
        return retained + replaced

    def candidate_predict(self, anchors: np.ndarray, donors: np.ndarray) -> np.ndarray:
        """Eq. (3) on Eq. (2)'s full recombination; the mask is applied again."""
        return self.predict(self.recombine(anchors, donors))

    def bypass_predict(self, anchors: np.ndarray, donors: np.ndarray) -> np.ndarray:
        """Eq. (26), included only to test the paper's exact bypass identity."""
        retained, _ = self.partition(anchors)
        probs = _softmax(retained @ self.classifier_w + self.classifier_b)
        classes = probs.shape[-1]
        tau = self.probability_floor
        return (1.0 - classes * tau) * probs + tau


@dataclass(frozen=True)
class FrozenCompatibilityTeacher:
    """Frozen 2048→256→C analogue used for Eq. (4)."""

    hidden_w: np.ndarray
    hidden_b: np.ndarray
    output_w: np.ndarray
    output_b: np.ndarray

    def embed(self, features: np.ndarray) -> np.ndarray:
        return np.maximum(features @ self.hidden_w + self.hidden_b, 0.0)

    def predict(self, features: np.ndarray) -> np.ndarray:
        return _softmax(self.embed(features) @ self.output_w + self.output_b)

    def accepted(
        self,
        candidates: np.ndarray,
        anchors: np.ndarray,
        labels: np.ndarray,
        gamma: float,
        distance_threshold: float,
    ) -> np.ndarray:
        label_probability = self.predict(candidates)[
            np.arange(len(candidates)), labels
        ]
        distance = np.linalg.norm(self.embed(candidates) - self.embed(anchors), axis=1)
        return (label_probability >= gamma) & (distance <= distance_threshold)


def kl_divergence(reference: np.ndarray, prediction: np.ndarray) -> np.ndarray:
    """Row-wise KL(reference || prediction), with stable positive probabilities."""
    tiny = np.finfo(float).tiny
    p = np.clip(reference, tiny, 1.0)
    q = np.clip(prediction, tiny, 1.0)
    return np.sum(p * (np.log(p) - np.log(q)), axis=-1)
