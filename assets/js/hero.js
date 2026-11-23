const ModelViewer = customElements.get('model-viewer');
if (ModelViewer) {
    ModelViewer.minimumRenderScale = 1;
}

const viewer = document.querySelector('#hero-model');

if (viewer) {
    initHero(viewer);
}

/**
 * Shows load progress and frames the boiler once model-viewer has it.
 */
function initHero(viewer) {
    const loaderBox = document.querySelector('#hero-loader');
    const loaderLabel = document.querySelector('#hero-loader-label');
    const loaderBar = document.querySelector('#hero-loader-bar');

    /**
     * Writes the download percentage into the hero loading line.
     */
    function showProgress(percent) {
        if (loaderLabel) {
            loaderLabel.textContent = 'Učitavanje kotlovnice · ' + percent + '%';
        }
        if (loaderBar) {
            loaderBar.style.width = percent + '%';
        }
    }

    viewer.addEventListener('progress', (event) => {
        showProgress(Math.round((event.detail?.totalProgress ?? 0) * 100));
    });

    viewer.addEventListener('load', () => {
        frameModel(viewer);
        showProgress(100);
        loaderBox?.classList.add('is-done');
        prepareMaterials(viewer);
    });

    viewer.addEventListener('error', () => {
        if (loaderLabel) {
            loaderLabel.textContent = 'Model kotlovnice nije moguće prikazati.';
        }
        if (loaderBar) {
            loaderBar.style.width = '0';
        }
    });
}

/**
 * Draws both sides of CAD surfaces so thin pipes stay visible from the camera.
 */
async function prepareMaterials(viewer) {
    const materials = viewer.model?.materials ?? [];

    await Promise.all(materials.map(async (material) => {
        try {
            await material.ensureLoaded();
            material.setDoubleSided(true);
        } catch (error) {
            // One unfinished material should not hide the rest of the room.
        }
    }));
}

/**
 * Frames the boiler inside its own panel. Wide screens give that panel the right 40%.
 */
function frameModel(viewer) {
    const box = contentBox(viewer);
    const center = box?.center ?? viewer.getBoundingBoxCenter();
    const size = box?.size ?? viewer.getDimensions();
    const radius = Math.max(size.x, size.y, size.z) * 0.5;

    if (!Number.isFinite(radius) || radius === 0) {
        return;
    }

    const fov = (32 * Math.PI) / 180;
    const aspect = viewer.clientWidth / Math.max(viewer.clientHeight, 1);
    const tall = size.y * 0.5;
    const wide = Math.max(size.x, size.z) * 0.5;
    const narrow = window.matchMedia('(max-width: 760px)').matches;
    let distance = (tall / Math.sin(fov / 2)) * (narrow ? 1.15 : 0.96);

    if (aspect > 0) {
        const horizontal = 2 * Math.atan(Math.tan(fov / 2) * aspect);
        const fitWidth = (wide / Math.sin(horizontal / 2)) * (narrow ? 1.08 : 0.9);
        distance = Math.max(distance, fitWidth);
    }

    const dy = narrow ? radius * 0.08 : 0;
    const orbitRadius = Math.hypot(dy, distance);
    const phi = (Math.acos(Math.max(-1, Math.min(1, dy / orbitRadius))) * 180) / Math.PI;

    viewer.minCameraOrbit = `auto auto ${distance * 0.45}m`;
    viewer.maxCameraOrbit = `auto auto ${distance * 2.8}m`;
    viewer.cameraTarget = `${center.x}m ${center.y}m ${center.z}m`;
    viewer.cameraOrbit = `0deg ${phi}deg ${orbitRadius}m`;
    viewer.fieldOfView = '32deg';
    viewer.jumpCameraToGoal();
    requestAnimationFrame(() => viewer.jumpCameraToGoal());
}

/**
 * Builds a box around the dense part of the export, leaving out stray high and low parts.
 */
function contentBox(viewer) {
    const sceneSymbol = Object.getOwnPropertySymbols(viewer)
        .find((symbol) => String(symbol) === 'Symbol(scene)');
    const root = sceneSymbol ? viewer[sceneSymbol]?.target?.children?.[0] : null;

    if (!root?.traverse || !root.matrixWorld?.clone) {
        return null;
    }

    root.updateWorldMatrix(true, true);
    const inverse = root.matrixWorld.clone().invert();
    const boxes = [];

    root.traverse((child) => {
        if (!child.isMesh || !child.geometry) {
            return;
        }

        if (!child.geometry.boundingBox) {
            child.geometry.computeBoundingBox();
        }

        const bounds = child.geometry.boundingBox;
        if (!bounds) {
            return;
        }

        const m = child.matrixWorld.clone().premultiply(inverse).elements;
        let minX = Infinity;
        let minY = Infinity;
        let minZ = Infinity;
        let maxX = -Infinity;
        let maxY = -Infinity;
        let maxZ = -Infinity;

        [bounds.min.x, bounds.max.x].forEach((x) => {
            [bounds.min.y, bounds.max.y].forEach((y) => {
                [bounds.min.z, bounds.max.z].forEach((z) => {
                    const wx = m[0] * x + m[4] * y + m[8] * z + m[12];
                    const wy = m[1] * x + m[5] * y + m[9] * z + m[13];
                    const wz = m[2] * x + m[6] * y + m[10] * z + m[14];
                    minX = Math.min(minX, wx);
                    minY = Math.min(minY, wy);
                    minZ = Math.min(minZ, wz);
                    maxX = Math.max(maxX, wx);
                    maxY = Math.max(maxY, wy);
                    maxZ = Math.max(maxZ, wz);
                });
            });
        });

        if ((maxX - minX) ** 2 + (maxY - minY) ** 2 + (maxZ - minZ) ** 2 === 0) {
            return;
        }

        boxes.push({
            minX, minY, minZ, maxX, maxY, maxZ,
            cy: (minY + maxY) / 2,
        });
    });

    if (boxes.length === 0) {
        return null;
    }

    const heights = boxes.map((box) => box.cy).sort((a, b) => a - b);
    const low = heights[Math.floor(heights.length * 0.04)];
    const high = heights[Math.floor(heights.length * 0.9)];
    let minX = Infinity;
    let minY = Infinity;
    let minZ = Infinity;
    let maxX = -Infinity;
    let maxY = -Infinity;
    let maxZ = -Infinity;

    boxes.forEach((box) => {
        if (box.cy < low || box.cy > high) {
            return;
        }

        minX = Math.min(minX, box.minX);
        minY = Math.min(minY, box.minY);
        minZ = Math.min(minZ, box.minZ);
        maxX = Math.max(maxX, box.maxX);
        maxY = Math.max(maxY, box.maxY);
        maxZ = Math.max(maxZ, box.maxZ);
    });

    if (!Number.isFinite(minX)) {
        return null;
    }

    return {
        center: {
            x: (minX + maxX) / 2,
            y: (minY + maxY) / 2,
            z: (minZ + maxZ) / 2,
        },
        size: {
            x: maxX - minX,
            y: maxY - minY,
            z: maxZ - minZ,
        },
    };
}
