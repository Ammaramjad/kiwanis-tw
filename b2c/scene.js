import * as THREE from "three";
import { RoundedBoxGeometry } from "./vendor/RoundedBoxGeometry.js";

const canvas = document.getElementById("scene");
const renderer = new THREE.WebGLRenderer({ canvas, antialias: true, alpha: true });
renderer.setPixelRatio(Math.min(devicePixelRatio, 2));
renderer.shadowMap.enabled = true;
renderer.toneMapping = THREE.ACESFilmicToneMapping;
renderer.toneMappingExposure = 1.05;

const scene = new THREE.Scene();
const camera = new THREE.PerspectiveCamera(28, 1, 0.1, 80);
camera.position.set(5.8, 2.4, 8.2);

scene.add(new THREE.HemisphereLight(0xfff6ec, 0xb9a48c, 1.15));
const key = new THREE.DirectionalLight(0xffffff, 1.55);
key.position.set(5, 9, 6);
key.castShadow = true;
scene.add(key);
scene.add(new THREE.DirectionalLight(0xfb7956, 0.55).translateX(-7).translateY(3).translateZ(-3));
const fill = new THREE.PointLight(0xffe2b0, 8, 20);
fill.position.set(-1, 3, 4);
scene.add(fill);

function box(w, h, d, r, color, metal = 0.18, rough = 0.28) {
  const m = new THREE.Mesh(
    new RoundedBoxGeometry(w, h, d, 5, r),
    new THREE.MeshPhysicalMaterial({
      color,
      metalness: metal,
      roughness: rough,
      clearcoat: 0.85,
      clearcoatRoughness: 0.18,
    })
  );
  m.castShadow = true;
  m.receiveShadow = true;
  return m;
}

const shuttle = new THREE.Group();

const hull = box(4.2, 1.15, 1.85, 0.28, 0xf4f0ea, 0.22, 0.24);
hull.position.y = 0.92;
shuttle.add(hull);

const skirt = box(4.15, 0.18, 1.88, 0.08, 0x2b241c, 0.55, 0.3);
skirt.position.y = 0.42;
shuttle.add(skirt);

const cabin = box(1.85, 0.78, 1.72, 0.2, 0xefeae3, 0.16, 0.22);
cabin.position.set(0.55, 1.68, 0);
shuttle.add(cabin);

const glass = new THREE.MeshPhysicalMaterial({
  color: 0x1c2733,
  metalness: 0.05,
  roughness: 0.08,
  transparent: true,
  opacity: 0.88,
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

const nose = box(0.72, 0.62, 1.62, 0.18, 0xfb7956, 0.12, 0.38);
nose.position.set(2.15, 0.78, 0);
shuttle.add(nose);

function lamp(z) {
  const l = new THREE.Mesh(
    new THREE.SphereGeometry(0.09, 20, 20),
    new THREE.MeshStandardMaterial({ color: 0xfff4d2, emissive: 0xffc56a, emissiveIntensity: 2 })
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
    new THREE.MeshStandardMaterial({ color: 0x1a1a1a, roughness: 0.65 })
  );
  tire.rotation.y = Math.PI / 2;
  const hub = new THREE.Mesh(
    new THREE.CylinderGeometry(0.14, 0.14, 0.12, 18),
    new THREE.MeshStandardMaterial({ color: 0xdfc48a, metalness: 0.8, roughness: 0.22 })
  );
  hub.rotation.z = Math.PI / 2;
  g.add(tire, hub);
  g.position.set(x, 0.36, z);
  shuttle.add(g);
  return g;
}
const wheels = [wheel(-1.2, 0.95), wheel(1.15, 0.95), wheel(-1.2, -0.95), wheel(1.15, -0.95)];

const roof = box(1.35, 0.08, 0.85, 0.04, 0x3a4656, 0.35, 0.32);
roof.position.set(0.45, 2.1, 0);
shuttle.add(roof);

shuttle.position.set(-1.6, 0, 0);
scene.add(shuttle);

const rings = [1.9, 2.55, 3.25].map((r, i) => {
  const mesh = new THREE.Mesh(
    new THREE.TorusGeometry(r, 0.01, 10, 90),
    new THREE.MeshBasicMaterial({ color: i === 1 ? 0xfb7956 : 0xd7b56a, transparent: true, opacity: 0.32 })
  );
  mesh.position.x = -1.6;
  scene.add(mesh);
  return mesh;
});

const ground = new THREE.Mesh(
  new THREE.CircleGeometry(2.4, 48),
  new THREE.ShadowMaterial({ opacity: 0.18 })
);
ground.rotation.x = -Math.PI / 2;
ground.position.set(-1.6, 0.01, 0);
ground.receiveShadow = true;
scene.add(ground);

function resize() {
  renderer.setSize(innerWidth, innerHeight, false);
  camera.aspect = innerWidth / innerHeight;
  camera.updateProjectionMatrix();
}
resize();
window.addEventListener("resize", resize);

const mouse = { x: 0, y: 0 };
window.addEventListener("pointermove", (e) => {
  mouse.x = (e.clientX / innerWidth) * 2 - 1;
  mouse.y = (e.clientY / innerHeight) * 2 - 1;
});

const clock = new THREE.Clock();
function tick() {
  const t = clock.getElapsedTime();
  shuttle.position.y = Math.sin(t * 1.05) * 0.16;
  shuttle.rotation.y = -0.7 + t * 0.22 + mouse.x * 0.4;
  shuttle.rotation.x = 0.08 + mouse.y * 0.07;
  wheels.forEach((w) => {
    w.rotation.x = t * 2.2;
  });
  rings.forEach((r, i) => {
    r.rotation.x = Math.PI / 2.15 + Math.sin(t * 0.35 + i) * 0.1;
    r.rotation.z = t * (0.1 + i * 0.04);
  });
  camera.position.x = 5.8 + mouse.x * 0.45;
  camera.position.y = 2.4 - mouse.y * 0.2;
  camera.lookAt(-1.5, 0.95, 0);
  renderer.render(scene, camera);
  requestAnimationFrame(tick);
}
tick();
