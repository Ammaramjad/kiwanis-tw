import * as THREE from "./vendor/three.module.js";

const canvas = document.getElementById("scene");
const renderer = new THREE.WebGLRenderer({ canvas, antialias: true, alpha: true });
renderer.setPixelRatio(Math.min(devicePixelRatio, 2));
renderer.shadowMap.enabled = true;
renderer.toneMapping = THREE.ACESFilmicToneMapping;
renderer.toneMappingExposure = 1.15;

const scene = new THREE.Scene();
const camera = new THREE.PerspectiveCamera(32, 1, 0.1, 80);
camera.position.set(4.6, 1.7, 6.4);

const hemi = new THREE.HemisphereLight(0xfff4ea, 0x344154, 1.1);
scene.add(hemi);
const key = new THREE.DirectionalLight(0xffffff, 1.8);
key.position.set(6, 8, 4);
key.castShadow = true;
scene.add(key);
const rim = new THREE.DirectionalLight(0xfb7956, 0.85);
rim.position.set(-6, 3, -4);
scene.add(rim);
const fill = new THREE.PointLight(0xffe7b0, 12, 18);
fill.position.set(0, 2.2, 3);
scene.add(fill);

function roundedBox(w, h, d, r, color, metal = 0.15, rough = 0.32) {
  const geo = new THREE.BoxGeometry(w, h, d);
  const mat = new THREE.MeshPhysicalMaterial({
    color,
    metalness: metal,
    roughness: rough,
    clearcoat: 0.7,
    clearcoatRoughness: 0.2,
  });
  const m = new THREE.Mesh(geo, mat);
  m.castShadow = true;
  m.receiveShadow = true;
  return m;
}

const shuttle = new THREE.Group();

const body = roundedBox(3.8, 1.05, 1.7, 0.2, 0xf3f1ec, 0.35, 0.22);
body.position.y = 0.85;
shuttle.add(body);

const belt = roundedBox(3.82, 0.12, 1.72, 0.05, 0x1b1611, 0.6, 0.25);
belt.position.y = 0.55;
shuttle.add(belt);

const cabin = roundedBox(1.7, 0.72, 1.62, 0.1, 0xe8e6e1, 0.25, 0.2);
cabin.position.set(0.85, 1.55, 0);
shuttle.add(cabin);

const glassMat = new THREE.MeshPhysicalMaterial({
  color: 0x1a2430,
  metalness: 0.1,
  roughness: 0.05,
  transmission: 0.35,
  thickness: 0.4,
  transparent: true,
  opacity: 0.92,
});
const windshield = new THREE.Mesh(new THREE.BoxGeometry(0.08, 0.52, 1.4), glassMat);
windshield.position.set(1.68, 1.55, 0);
shuttle.add(windshield);

[-0.55, 0.2, 0.95].forEach((x) => {
  const win = new THREE.Mesh(new THREE.BoxGeometry(0.55, 0.38, 0.04), glassMat);
  win.position.set(x, 1.52, 0.82);
  shuttle.add(win);
  const win2 = win.clone();
  win2.position.z = -0.82;
  shuttle.add(win2);
});

const nose = roundedBox(0.55, 0.7, 1.55, 0.1, 0xfb7956, 0.2, 0.35);
nose.position.set(1.95, 0.78, 0);
shuttle.add(nose);

const bumper = roundedBox(0.18, 0.22, 1.5, 0.04, 0x2a241c, 0.7, 0.3);
bumper.position.set(2.18, 0.48, 0);
shuttle.add(bumper);

function lamp(x, z) {
  const l = new THREE.Mesh(
    new THREE.SphereGeometry(0.08, 16, 16),
    new THREE.MeshStandardMaterial({ color: 0xfff2c8, emissive: 0xffc56a, emissiveIntensity: 2.2 })
  );
  l.position.set(x, 0.72, z);
  shuttle.add(l);
}
lamp(2.16, 0.58);
lamp(2.16, -0.58);

function wheel(x, z) {
  const g = new THREE.Group();
  const tire = new THREE.Mesh(
    new THREE.CylinderGeometry(0.34, 0.34, 0.22, 24),
    new THREE.MeshStandardMaterial({ color: 0x1a1a1a, roughness: 0.7 })
  );
  tire.rotation.z = Math.PI / 2;
  tire.castShadow = true;
  const hub = new THREE.Mesh(
    new THREE.CylinderGeometry(0.16, 0.16, 0.24, 16),
    new THREE.MeshStandardMaterial({ color: 0xd9c48a, metalness: 0.85, roughness: 0.2 })
  );
  hub.rotation.z = Math.PI / 2;
  g.add(tire, hub);
  g.position.set(x, 0.34, z);
  shuttle.add(g);
  return g;
}
const wheels = [wheel(-1.15, 0.92), wheel(1.05, 0.92), wheel(-1.15, -0.92), wheel(1.05, -0.92)];

const roof = roundedBox(1.4, 0.06, 0.9, 0.02, 0x344154, 0.4, 0.3);
roof.position.set(0.7, 1.96, 0);
shuttle.add(roof);

shuttle.rotation.y = -0.55;
shuttle.position.x = -1.35;
scene.add(shuttle);

const rings = [];
[1.8, 2.4, 3.1].forEach((r, i) => {
  const torus = new THREE.Mesh(
    new THREE.TorusGeometry(r, 0.012, 12, 80),
    new THREE.MeshBasicMaterial({ color: i === 1 ? 0xfb7956 : 0xd4b36a, transparent: true, opacity: 0.35 })
  );
  torus.rotation.x = Math.PI / 2.3 + i * 0.15;
  scene.add(torus);
  rings.push(torus);
});

const shadow = new THREE.Mesh(
  new THREE.CircleGeometry(2.1, 40),
  new THREE.ShadowMaterial({ opacity: 0.22 })
);
shadow.rotation.x = -Math.PI / 2;
shadow.position.y = 0.01;
shadow.receiveShadow = true;
scene.add(shadow);

function resize() {
  const w = innerWidth;
  const h = innerHeight;
  renderer.setSize(w, h, false);
  camera.aspect = w / h;
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
  shuttle.position.y = Math.sin(t * 1.05) * 0.14;
  shuttle.rotation.y = -0.55 + t * 0.18 + mouse.x * 0.35;
  shuttle.rotation.x = mouse.y * 0.08;
  wheels.forEach((w) => {
    w.rotation.x = t * 2.4;
  });
  rings.forEach((r, i) => {
    r.rotation.z = t * (0.12 + i * 0.05);
    r.rotation.x = Math.PI / 2.3 + Math.sin(t * 0.3 + i) * 0.08;
  });
  camera.position.x = 4.6 + mouse.x * 0.4;
  camera.position.y = 1.7 + mouse.y * -0.2;
  camera.lookAt(-1.1, 0.85, 0);
  renderer.render(scene, camera);
  requestAnimationFrame(tick);
}
tick();
