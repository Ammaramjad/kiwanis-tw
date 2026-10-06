const figure = document.getElementById("codireFigure");
const shell = document.getElementById("canvasShell");
const toast = document.getElementById("toast");
const stageButtons = [...document.querySelectorAll(".stage-button")];
let zoom = 1;
let toastTimer;

function notify(message) {
  clearTimeout(toastTimer);
  toast.textContent = message;
  toast.classList.add("show");
  toastTimer = setTimeout(() => toast.classList.remove("show"), 2800);
}

function selectStage(stage) {
  figure.dataset.focus = stage;
  stageButtons.forEach((button) => {
    const selected = button.dataset.stage === stage;
    button.classList.toggle("is-active", selected);
    button.setAttribute("aria-pressed", selected ? "true" : "false");
  });
}

stageButtons.forEach((button) => {
  button.addEventListener("click", () => selectStage(button.dataset.stage));
});

figure.querySelectorAll(".stage").forEach((stage) => {
  stage.addEventListener("click", () => selectStage(stage.dataset.stage));
});

document.getElementById("showNumbers").addEventListener("change", (event) => {
  figure.classList.toggle("hide-numbers", !event.target.checked);
});

document.getElementById("showNotation").addEventListener("change", (event) => {
  figure.classList.toggle("hide-notation", !event.target.checked);
});

document.getElementById("highContrast").addEventListener("change", (event) => {
  figure.classList.toggle("high-contrast", event.target.checked);
});

function applyZoom(nextZoom) {
  zoom = Math.min(1.8, Math.max(.7, nextZoom));
  figure.style.width = `${zoom * 100}%`;
  document.getElementById("zoomLabel").textContent = `${Math.round(zoom * 100)}%`;
}

document.getElementById("zoomIn").addEventListener("click", () => applyZoom(zoom + .1));
document.getElementById("zoomOut").addEventListener("click", () => applyZoom(zoom - .1));
document.getElementById("resetView").addEventListener("click", () => {
  applyZoom(1);
  selectStage("all");
  shell.scrollTo({ top: 0, left: 0, behavior: "smooth" });
});

function collectStyles() {
  let css = "";
  [...document.styleSheets].forEach((sheet) => {
    try {
      [...sheet.cssRules].forEach((rule) => {
        css += `${rule.cssText}\n`;
      });
    } catch {
      // Cross-origin font styles are not required for the standalone artwork.
    }
  });
  return css;
}

function serializedFigure() {
  const clone = figure.cloneNode(true);
  clone.removeAttribute("style");
  clone.setAttribute("width", "1600");
  clone.setAttribute("height", "900");
  clone.setAttribute("xmlns", "http://www.w3.org/2000/svg");
  const style = document.createElementNS("http://www.w3.org/2000/svg", "style");
  style.textContent = collectStyles();
  clone.insertBefore(style, clone.firstChild);
  return `<?xml version="1.0" encoding="UTF-8"?>\n${new XMLSerializer().serializeToString(clone)}`;
}

function download(blob, filename) {
  const url = URL.createObjectURL(blob);
  const anchor = document.createElement("a");
  anchor.href = url;
  anchor.download = filename;
  document.body.appendChild(anchor);
  anchor.click();
  anchor.remove();
  setTimeout(() => URL.revokeObjectURL(url), 1000);
}

document.getElementById("exportSvg").addEventListener("click", () => {
  const blob = new Blob([serializedFigure()], { type: "image/svg+xml;charset=utf-8" });
  download(blob, "CoDiRe_Figure_1_vector.svg");
  notify("Vector SVG exported · fully scalable");
});

function crc32(bytes) {
  let crc = 0xffffffff;
  for (let i = 0; i < bytes.length; i += 1) {
    crc ^= bytes[i];
    for (let bit = 0; bit < 8; bit += 1) {
      crc = (crc >>> 1) ^ ((crc & 1) ? 0xedb88320 : 0);
    }
  }
  return (crc ^ 0xffffffff) >>> 0;
}

function uint32(value) {
  return new Uint8Array([
    (value >>> 24) & 255,
    (value >>> 16) & 255,
    (value >>> 8) & 255,
    value & 255,
  ]);
}

function pngChunk(type, data) {
  const typeBytes = new TextEncoder().encode(type);
  const crcInput = new Uint8Array(typeBytes.length + data.length);
  crcInput.set(typeBytes);
  crcInput.set(data, typeBytes.length);
  const chunk = new Uint8Array(12 + data.length);
  chunk.set(uint32(data.length), 0);
  chunk.set(typeBytes, 4);
  chunk.set(data, 8);
  chunk.set(uint32(crc32(crcInput)), 8 + data.length);
  return chunk;
}

async function addDpiMetadata(blob, dpi) {
  const png = new Uint8Array(await blob.arrayBuffer());
  const pixelsPerMeter = Math.round(dpi / .0254);
  const data = new Uint8Array(9);
  data.set(uint32(pixelsPerMeter), 0);
  data.set(uint32(pixelsPerMeter), 4);
  data[8] = 1;
  const chunk = pngChunk("pHYs", data);
  const output = new Uint8Array(png.length + chunk.length);
  output.set(png.slice(0, 33), 0);
  output.set(chunk, 33);
  output.set(png.slice(33), 33 + chunk.length);
  return new Blob([output], { type: "image/png" });
}

function canvasBlob(canvas) {
  return new Promise((resolve, reject) => {
    canvas.toBlob((blob) => blob ? resolve(blob) : reject(new Error("PNG encoding failed")), "image/png");
  });
}

document.getElementById("exportPng").addEventListener("click", async (event) => {
  const button = event.currentTarget;
  button.disabled = true;
  button.textContent = "Rendering 6400 × 3600…";
  try {
    await document.fonts.ready;
    const source = new Blob([serializedFigure()], { type: "image/svg+xml;charset=utf-8" });
    const sourceUrl = URL.createObjectURL(source);
    const image = new Image();
    await new Promise((resolve, reject) => {
      image.onload = resolve;
      image.onerror = () => reject(new Error("The SVG could not be rasterized"));
      image.src = sourceUrl;
    });
    const canvas = document.createElement("canvas");
    canvas.width = 6400;
    canvas.height = 3600;
    const context = canvas.getContext("2d", { alpha: false });
    context.fillStyle = "#ffffff";
    context.fillRect(0, 0, canvas.width, canvas.height);
    context.drawImage(image, 0, 0, canvas.width, canvas.height);
    URL.revokeObjectURL(sourceUrl);
    const png = await addDpiMetadata(await canvasBlob(canvas), 900);
    download(png, "CoDiRe_Figure_1_900DPI.png");
    notify("900-DPI PNG exported · 6400 × 3600 px");
  } catch (error) {
    console.error(error);
    notify("Export failed. Use SVG export as the publication-safe fallback.");
  } finally {
    button.disabled = false;
    button.innerHTML = "<span>Export</span> PNG · 900 DPI";
  }
});

selectStage("all");
