import * as THREE from "three";
import { RoundedBoxGeometry } from "./vendor/RoundedBoxGeometry.js";

const canvas = document.getElementById("scene");
const renderer = new THREE.WebGLRenderer({ canvas, antialias: true, alpha: true });
renderer.setPixelRatio(Math.min(devicePixelRatio, 2));
renderer.shadowMap.enabled = true;
renderer.toneMapping = THREE.ACESFilmicToneMapping;
renderer.toneMappingExposure = 1.12;

const scene = new THREE.Scene();
const camera = new THREE.PerspectiveCamera(28, 1, 0.1, 80);

scene.add(new THREE.HemisphereLight(0xb8e6df, 0x061018, 0.85));
const key = new THREE.DirectionalLight(0xffffff, 1.7);
key.position.set(6, 9, 5);
key.castShadow = true;
scene.add(key);
const rim = new THREE.DirectionalLight(0x5eead4, 0.9);
rim.position.set(-8, 3, -4);
scene.add(rim);
const fill = new THREE.PointLight(0x5eead4, 10, 22);
fill.position.set(0, 2.4, 4);
scene.add(fill);

function box(w, h, d, r, color, metal = 0.35, rough = 0.28) {
  const m = new THREE.Mesh(
    new RoundedBoxGeometry(w, h, d, 6, r),
    new THREE.MeshPhysicalMaterial({
      color,
      metalness: metal,
      roughness: rough,
      clearcoat: 0.9,
      clearcoatRoughness: 0.16,
    })
  );
  m.castShadow = true;
  m.receiveShadow = true;
  return m;
}

const shuttle = new THREE.Group();
const hull = box(4.2, 1.15, 1.85, 0.28, 0xc5d4d8, 0.55, 0.22);
hull.position.set(0, 0.92, 0);
const skirt = box(4.15, 0.18, 1.88, 0.08, 0x102028, 0.6, 0.3);
skirt.position.set(0, 0.42, 0);
const cabin = box(1.85, 0.78, 1.72, 0.2, 0xd7e4e6, 0.4, 0.2);
cabin.position.set(0.55, 1.68, 0);
shuttle.add(hull, skirt, cabin);

const glass = new THREE.MeshPhysicalMaterial({
  color: 0x082028,
  metalness: 0.1,
  roughness: 0.06,
  transparent: true,
  opacity: 0.9,
  clearcoat: 1,
});
const windshield = new THREE.Mesh(new RoundedBoxGeometry(0.1, 0.58, 1.48, 3, 0.06), glass);
windshield.position.set(1.48, 1.68, 0);
windshield.rotation.z = -0.12;
shuttle.add(windshield);
[-0.7, 0.15, 0.95].forEach((x) => {
  const w = new THREE.Mesh(new RoundedBoxGeometry(0.58, 0.4, 0.05, 2, 0.04), glass);
  w.position.set(x, 1.66, 0.86);
  shuttle.add(w);
  const w2 = w.clone();
  w2.position.z = -0.86;
  shuttle.add(w2);
});

const nose = box(0.72, 0.62, 1.62, 0.18, 0x14b8a6, 0.2, 0.35);
nose.position.set(2.15, 0.78, 0);
shuttle.add(nose);

function lamp(z) {
  const l = new THREE.Mesh(
    new THREE.SphereGeometry(0.09, 20, 20),
    new THREE.MeshStandardMaterial({ color: 0xe0fff8, emissive: 0x5eead4, emissiveIntensity: 2.4 })
  );
  l.position.set(2.48, 0.74, z);
  shuttle.add(l);
}
lamp(0.55);
lamp(-0.55);

function wheel(x, z) {
  const g = new THREE.Group();
  const tire = new THREE.Mesh(
    new THREE.TorusGeometry(0.32, 0.1, 14, 28),
    new THREE.MeshStandardMaterial({ color: 0x111111, roughness: 0.65 })
  );
  tire.rotation.y = Math.PI / 2;
  const hub = new THREE.Mesh(
    new THREE.CylinderGeometry(0.14, 0.14, 0.12, 18),
    new THREE.MeshStandardMaterial({ color: 0x5eead4, metalness: 0.75, roughness: 0.2 })
  );
  hub.rotation.z = Math.PI / 2;
  g.add(tire, hub);
  g.position.set(x, 0.36, z);
  shuttle.add(g);
  return g;
}
const wheels = [wheel(-1.2, 0.95), wheel(1.15, 0.95), wheel(-1.2, -0.95), wheel(1.15, -0.95)];
const roof = box(1.35, 0.08, 0.85, 0.04, 0x0b1c22, 0.4, 0.32);
roof.position.set(0.45, 2.1, 0);
shuttle.add(roof);
scene.add(shuttle);

const rings = [1.9, 2.55, 3.25].map((r, i) => {
  const mesh = new THREE.Mesh(
    new THREE.TorusGeometry(r, 0.012, 10, 90),
    new THREE.MeshBasicMaterial({ color: i === 1 ? 0x5eead4 : 0x94a3b8, transparent: true, opacity: 0.28 })
  );
  scene.add(mesh);
  return mesh;
});

const ground = new THREE.Mesh(new THREE.CircleGeometry(2.6, 48), new THREE.ShadowMaterial({ opacity: 0.28 }));
ground.rotation.x = -Math.PI / 2;
ground.receiveShadow = true;
scene.add(ground);

const shots = [
  { cam: [5.6, 2.3, 8.0], look: [-0.2, 1.0, 0], fov: 28 },
  { cam: [2.1, 1.25, 3.2], look: [1.4, 1.15, 0], fov: 20 },
  { cam: [-7.2, 1.7, 2.6], look: [0.1, 1.0, 0], fov: 30 },
  { cam: [0.4, 5.4, -7.4], look: [0.0, 0.8, 0], fov: 36 },
  { cam: [8.6, 1.35, 1.2], look: [0.2, 0.95, 0], fov: 24 },
  { cam: [0.2, 2.4, 10.6], look: [0.0, 1.0, 0], fov: 32 },
];

const state = {
  scroll: 0,
  zoom: 1,
  pan: 0,
  mouse: { x: 0, y: 0 },
};

window.fleetCam = {
  zoomIn() {
    state.zoom = Math.max(0.55, state.zoom - 0.12);
  },
  zoomOut() {
    state.zoom = Math.min(1.7, state.zoom + 0.12);
  },
  pan(dir) {
    state.pan = THREE.MathUtils.clamp(state.pan + dir * 1.4, -8, 8);
  },
};

function progress() {
  const max = document.documentElement.scrollHeight - innerHeight;
  return max <= 0 ? 0 : window.scrollY / max;
}

function sample(p) {
  const n = shots.length - 1;
  const x = p * n;
  const i = Math.min(n - 1, Math.floor(x));
  const t = x - i;
  const a = shots[i];
  const b = shots[i + 1];
  const mix = (u, v) => u.map((n, idx) => n + (v[idx] - n) * t);
  return {
    cam: mix(a.cam, b.cam),
    look: mix(a.look, b.look),
    fov: a.fov + (b.fov - a.fov) * t,
  };
}

function resize() {
  renderer.setSize(innerWidth, innerHeight, false);
  camera.aspect = innerWidth / innerHeight;
  camera.updateProjectionMatrix();
}
resize();
window.addEventListener("resize", resize);
window.addEventListener("pointermove", (e) => {
  state.mouse.x = (e.clientX / innerWidth) * 2 - 1;
  state.mouse.y = (e.clientY / innerHeight) * 2 - 1;
});
window.addEventListener("scroll", () => {
  state.scroll += (progress() - state.scroll) * 0.35;
}, { passive: true });

const clock = new THREE.Clock();
const camPos = new THREE.Vector3();
const look = new THREE.Vector3();

function tick() {
  const t = clock.getElapsedTime();
  state.scroll += (progress() - state.scroll) * 0.08;
  const shot = sample(state.scroll);
  shuttle.position.y = Math.sin(t * 1.05) * 0.12;
  shuttle.rotation.y = -0.55 + t * 0.12 + state.mouse.x * 0.12;
  wheels.forEach((w) => {
    w.rotation.x = t * 2.1;
  });
  rings.forEach((r, i) => {
    r.rotation.x = Math.PI / 2.15 + Math.sin(t * 0.3 + i) * 0.08;
    r.rotation.z = t * (0.08 + i * 0.03);
  });

  const z = state.zoom;
  camPos.set(
    shot.cam[0] * z + state.pan + state.mouse.x * 0.25,
    shot.cam[1] * (0.85 + z * 0.15),
    shot.cam[2] * z
  );
  look.set(shot.look[0], shot.look[1], shot.look[2]);
  camera.fov += (shot.fov / z - camera.fov) * 0.08;
  camera.position.lerp(camPos, 0.08);
  camera.lookAt(look);
  camera.updateProjectionMatrix();
  renderer.render(scene, camera);
  requestAnimationFrame(tick);
}
tick();
