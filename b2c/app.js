const copy = {
  airport: {
    kicker: "Airport corridors · meet & greet",
    title: "Your chauffeur is already moving toward you.",
    lede: "Track the flight, hold at arrivals, and open the door on time. Pickup and drop-off stay on one ticket.",
    stamp: "Flight-aware",
    quoteLabel: "Airport transfer fare",
    pickup: "Taoyuan International Airport (TPE)",
    dropoff: "Taipei 101, Xinyi",
  },
  p2p: {
    kicker: "Door to door · city transfer",
    title: "Point to point, without the waiting game.",
    lede: "Name the two doors. A professional driver takes the shortest quiet route between them.",
    stamp: "Fixed fare",
    quoteLabel: "Point-to-point fare",
    pickup: "Grand Hyatt Taipei",
    dropoff: "Jiufen Old Street",
  },
  hourly: {
    kicker: "Car with driver · on standby",
    title: "Keep the car. Keep the driver. Keep the day.",
    lede: "From meetings to dinner, the same chauffeur stays with you. Pickup, drop-off, and hours on one form.",
    stamp: "Hourly charter",
    quoteLabel: "With-driver fare",
    pickup: "Songshan Airport (TSA)",
    dropoff: "Return to hotel or next stop",
  },
  multi: {
    kicker: "Corridor travel · several cities",
    title: "Several cities. One car. One driver.",
    lede: "String Taipei, Taichung, Tainan into a single movement. Add cities without leaving this ticket.",
    stamp: "Multi-city",
    quoteLabel: "Multi-city fare",
    pickup: "Taipei",
    dropoff: "Kaohsiung",
  },
};

const modes = ["airport", "p2p", "hourly", "multi"];
const tabs = [...document.querySelectorAll(".tab")];
const ink = document.getElementById("tabInk");
const pickup = document.getElementById("pickup");
const dropoff = document.getElementById("dropoff");
const hours = document.getElementById("hours");
const pax = document.getElementById("pax");
const flight = document.getElementById("flight");
const when = document.getElementById("when");
const fareEl = document.getElementById("fare");
const stops = document.getElementById("stops");
const toast = document.getElementById("toast");

function nextHour() {
  const d = new Date();
  d.setMinutes(0, 0, 0);
  d.setHours(d.getHours() + 3);
  const pad = (n) => String(n).padStart(2, "0");
  return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
}

when.value = nextHour();

function placeInk(btn) {
  const parent = btn.parentElement.getBoundingClientRect();
  const box = btn.getBoundingClientRect();
  ink.style.width = `${box.width}px`;
  ink.style.left = `${box.left - parent.left}px`;
  ink.style.transform = "none";
}

function setCopy(mode) {
  const c = copy[mode];
  document.querySelector('[data-copy="kicker"]').textContent = c.kicker;
  document.querySelector('[data-copy="title"]').textContent = c.title;
  document.querySelector('[data-copy="lede"]').textContent = c.lede;
  document.querySelector('[data-copy="stamp"]').textContent = c.stamp;
  document.querySelector('[data-copy="quoteLabel"]').textContent = c.quoteLabel;
  pickup.placeholder = c.pickup;
  dropoff.placeholder = c.dropoff;
  if (!pickup.value) pickup.value = c.pickup;
  if (!dropoff.value) dropoff.value = c.dropoff;
}

function quote() {
  const mode = document.body.dataset.mode;
  const people = Number(pax.value) || 1;
  const extra = people > 3 ? 300 : 0;
  let n = 2280;
  if (mode === "airport") n = 2280 + extra + (flight.value ? 0 : 80);
  if (mode === "p2p") n = 1680 + extra;
  if (mode === "hourly") n = 880 * Math.max(2, Number(hours.value) || 4) + extra;
  if (mode === "multi") n = 2480 + extra + stops.querySelectorAll("input").length * 900;
  fareEl.animate([{ transform: "translateY(6px)", opacity: 0.4 }, { transform: "none", opacity: 1 }], {
    duration: 280,
    easing: "ease-out",
  });
  fareEl.textContent = `NT$${n.toLocaleString()}`;
}

function activate(mode, btn) {
  document.body.dataset.mode = mode;
  tabs.forEach((t) => {
    const on = t === btn;
    t.classList.toggle("is-active", on);
    t.setAttribute("aria-selected", on ? "true" : "false");
  });
  const c = copy[mode];
  pickup.value = c.pickup;
  dropoff.value = c.dropoff;
  setCopy(mode);
  placeInk(btn);
  quote();
}

tabs.forEach((btn) => {
  btn.addEventListener("click", () => activate(btn.dataset.mode, btn));
});

document.getElementById("direction").addEventListener("click", (e) => {
  const b = e.target.closest("button");
  if (!b) return;
  [...e.currentTarget.children].forEach((x) => x.classList.toggle("is-on", x === b));
  if (b.dataset.dir === "arrival") {
    pickup.value = "Taoyuan International Airport (TPE)";
    dropoff.value = "Taipei 101, Xinyi";
  } else {
    pickup.value = "Taipei 101, Xinyi";
    dropoff.value = "Taoyuan International Airport (TPE)";
  }
  quote();
});

document.getElementById("swap").addEventListener("click", () => {
  const a = pickup.value;
  pickup.value = dropoff.value;
  dropoff.value = a;
  document.getElementById("swap").animate([{ transform: "rotate(180deg)" }, { transform: "none" }], { duration: 360 });
  quote();
});

document.getElementById("addStop").addEventListener("click", () => {
  const cities = ["Taichung", "Sun Moon Lake", "Tainan", "Alishan", "Hualien"];
  const used = [...stops.querySelectorAll("input")].map((i) => i.value);
  const next = cities.find((c) => !used.includes(c)) || `City ${used.length + 1}`;
  const row = document.createElement("div");
  row.className = "stop-row field";
  row.innerHTML = `<label class="field" style="margin:0;width:100%"><span>City stop</span><input value="${next}" /></label><button type="button" aria-label="Remove stop">Remove</button>`;
  row.querySelector("button").onclick = () => {
    row.remove();
    quote();
  };
  row.querySelector("input").addEventListener("input", quote);
  stops.appendChild(row);
  quote();
});

["input", "change"].forEach((ev) => {
  document.getElementById("book").addEventListener(ev, quote);
});

document.getElementById("book").addEventListener("submit", (e) => {
  e.preventDefault();
  const mode = document.body.dataset.mode;
  const label = tabs.find((t) => t.dataset.mode === mode).textContent.trim();
  toast.hidden = false;
  toast.textContent = `${label}: ${pickup.value} → ${dropoff.value} · ${fareEl.textContent}`;
  toast.animate([{ opacity: 0, transform: "translate(-50%, 12px)" }, { opacity: 1, transform: "translate(-50%, 0)" }], {
    duration: 280,
    fill: "forwards",
  });
  setTimeout(() => {
    toast.hidden = true;
  }, 3200);
});

const count = document.querySelector(".count");
setInterval(() => {
  const n = 18 + Math.floor(Math.random() * 12);
  count.textContent = n;
}, 2600);

window.addEventListener("resize", () => placeInk(document.querySelector(".tab.is-active")));
placeInk(document.querySelector(".tab.is-active"));
setCopy("airport");
pickup.value = copy.airport.pickup;
dropoff.value = copy.airport.dropoff;
quote();

document.addEventListener("keydown", (e) => {
  if (!["ArrowLeft", "ArrowRight"].includes(e.key)) return;
  if (!e.target.classList.contains("tab") && e.target !== document.body) return;
  const i = modes.indexOf(document.body.dataset.mode);
  const next = modes[(i + (e.key === "ArrowRight" ? 1 : -1) + modes.length) % modes.length];
  activate(next, tabs.find((t) => t.dataset.mode === next));
});
