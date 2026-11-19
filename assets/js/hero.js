import * as THREE from 'three';
import { GLTFLoader } from 'three/addons/loaders/GLTFLoader.js';
import { OrbitControls } from 'three/addons/controls/OrbitControls.js';
import { RoomEnvironment } from 'three/addons/environments/RoomEnvironment.js';

const canvas = document.querySelector('#hero-canvas');

if (canvas instanceof HTMLCanvasElement) {
    initHero(canvas);
}

/**
 * Builds the boiler-room viewer, loads the glTF export, and keeps it framed in the hero.
 */
function initHero(canvas) {
    const hero = canvas.parentElement;
    const loaderBox = document.querySelector('#hero-loader');
    const loaderLabel = document.querySelector('#hero-loader-label');
    const loaderBar = document.querySelector('#hero-loader-bar');
    const modelUrl = canvas.dataset.model;

    const renderer = new THREE.WebGLRenderer({
        canvas,
        antialias: true,
        alpha: false,
        powerPreference: 'high-performance',
    });
    renderer.outputColorSpace = THREE.SRGBColorSpace;
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    renderer.toneMappingExposure = 1.02;
    renderer.setClearColor(0xeef3f6, 1);

    const scene = new THREE.Scene();
    scene.background = new THREE.Color(0xeef3f6);

    const camera = new THREE.PerspectiveCamera(32, 1, 0.01, 100);
    const controls = new OrbitControls(camera, canvas);
    controls.enableDamping = true;
    controls.enablePan = false;
    controls.autoRotate = !window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    controls.autoRotateSpeed = 0.45;

    scene.add(new THREE.HemisphereLight(0xf4f8fb, 0xc5d3de, 1.05));
    const sun = new THREE.DirectionalLight(0xffffff, 1.7);
    sun.position.set(6, 10, 8);
    scene.add(sun);

    const environment = new THREE.PMREMGenerator(renderer);
    scene.environment = environment.fromScene(new RoomEnvironment(), 0.04).texture;
    environment.dispose();

    resizeHero(renderer, camera, hero);

    /**
     * Writes the download percentage into the hero loading line.
     */
    function showProgress(loaded, total) {
        const percent = total ? Math.round((loaded / total) * 100) : 0;
        if (loaderLabel) {
            loaderLabel.textContent = 'Učitavanje kotlovnice · ' + percent + '%';
        }
        if (loaderBar) {
            loaderBar.style.width = percent + '%';
        }
    }

    /**
     * Hides the loading line once the model is on screen.
     */
    function finishLoading() {
        loaderBox?.classList.add('is-done');
    }

    /**
     * Replaces the loading line with a short failure message.
     */
    function showError(message) {
        if (loaderLabel) {
            loaderLabel.textContent = message;
        }
        if (loaderBar) {
            loaderBar.style.width = '0';
        }
    }

    if (!modelUrl) {
        showError('Putanja do modela kotlovnice nije postavljena.');
        return;
    }

    const manager = new THREE.LoadingManager();
    manager.onProgress = (_url, loaded, total) => showProgress(loaded, total);

    const loader = new GLTFLoader(manager);
    loader.load(
        modelUrl,
        (gltf) => {
            const model = gltf.scene;
            prepareMaterials(model);
            scene.add(model);
            frameModel(camera, controls, model);
            finishLoading();
        },
        (event) => {
            if (event.total) {
                showProgress(event.loaded, event.total);
            }
        },
        () => {
            showError('Model kotlovnice nije moguće prikazati.');
        }
    );

    let running = true;

    /**
     * Draws the next frame while the tab stays visible.
     */
    function animate() {
        if (!running) {
            return;
        }
        controls.update();
        renderer.render(scene, camera);
        requestAnimationFrame(animate);
    }

    animate();

    window.addEventListener('resize', () => resizeHero(renderer, camera, hero));
    document.addEventListener('visibilitychange', () => {
        running = !document.hidden;
        if (running) {
            animate();
        }
    });
}

/**
 * Draws both sides of CAD surfaces so thin pipes stay visible from the camera.
 */
function prepareMaterials(model) {
    model.traverse((child) => {
        if (!child.isMesh) {
            return;
        }
        const materials = Array.isArray(child.material) ? child.material : [child.material];
        materials.forEach((material) => {
            if (material) {
                material.side = THREE.DoubleSide;
            }
        });
    });
}

/**
 * Builds a box around the dense part of the export, leaving out stray high and low parts.
 */
function contentBox(model) {
    const boxes = [];
    model.updateMatrixWorld(true);

    model.traverse((child) => {
        if (!child.isMesh || !child.geometry) {
            return;
        }
        if (!child.geometry.boundingBox) {
            child.geometry.computeBoundingBox();
        }
        if (!child.geometry.boundingBox) {
            return;
        }
        const box = child.geometry.boundingBox.clone().applyMatrix4(child.matrixWorld);
        if (box.getSize(new THREE.Vector3()).lengthSq() === 0) {
            return;
        }
        boxes.push(box);
    });

    if (boxes.length === 0) {
        return new THREE.Box3().setFromObject(model);
    }

    const heights = boxes
        .map((box) => box.getCenter(new THREE.Vector3()).y)
        .sort((a, b) => a - b);
    const low = heights[Math.floor(heights.length * 0.04)];
    const high = heights[Math.floor(heights.length * 0.9)];
    const kept = new THREE.Box3();

    boxes.forEach((box) => {
        const y = box.getCenter(new THREE.Vector3()).y;
        if (y >= low && y <= high) {
            kept.union(box);
        }
    });

    return kept.isEmpty() ? new THREE.Box3().setFromObject(model) : kept;
}

/**
 * Places the camera so the boiler room sits right of the headline.
 */
function frameModel(camera, controls, model) {
    const box = contentBox(model);
    const size = box.getSize(new THREE.Vector3());
    const center = box.getCenter(new THREE.Vector3());
    const radius = Math.max(size.x, size.y, size.z) * 0.5;

    if (!Number.isFinite(radius) || radius === 0) {
        return;
    }

    const distance = (radius / Math.sin((camera.fov * Math.PI) / 360)) * 0.92;
    camera.near = Math.max(distance / 200, 0.001);
    camera.far = distance * 40;
    camera.position.set(
        center.x + distance * 0.15,
        center.y + radius * 0.15,
        center.z + distance
    );
    camera.updateProjectionMatrix();
    controls.target.set(center.x - radius * 0.55, center.y + radius * 0.16, center.z);
    controls.minDistance = distance * 0.45;
    controls.maxDistance = distance * 2.8;
    controls.update();
}

/**
 * Matches the drawing buffer to the hero size without letting the canvas override CSS.
 */
function resizeHero(renderer, camera, hero) {
    const width = hero?.clientWidth || window.innerWidth;
    const height = hero?.clientHeight || window.innerHeight;
    renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 1.5));
    renderer.setSize(width, height, false);
    camera.aspect = width / Math.max(height, 1);
    camera.updateProjectionMatrix();
}
