// import {
//   css,
//   transform,
//   chain,
//   delay,
//   tween,
//   easing,
//   parallel,
// } from 'popmotion';
// const { interpolate } = transform;
import { interpolate, easeOut, tween, chain, delay, parallel } from "popmotion";
import { FC, useEffect, useRef } from "react";
import styles from "./Modal.module.scss";

let trigger;
let isClosing = false;

// Select DOM
const modalTriggersDom = document.querySelectorAll(".modal-trigger");
const dimmer = document.querySelector(".overlay");
const modalContainer = document.querySelector(".modal-container");
const modal = document.querySelector(".modal");

// Create CSS renderers
const dimmerRenderer = css(dimmer);
const modalContainerRenderer = css(modalContainer);
const modalRenderer = css(modal);

// Return the center x, y of a bounding box
const findCenter = ({ top, left, height, width }) => {
  return {
    x: left + width / 2,
    y: top + height / 2,
  };
};

/*
  Generate a function that will take a progress value (0 - 1)
  and use that to tween the modal from the source to the destination
  bounding boxes
*/
const vRange = [0, 1];
function generateModalTweener(sourceBBox: any, destinationBBox: any) {
  const sourceCenter = findCenter(sourceBBox);
  const destinationCenter = findCenter(destinationBBox);

  const toX = interpolate(vRange, [sourceCenter.x - destinationCenter.x, 0]);
  const toY = interpolate(vRange, [sourceCenter.y - destinationCenter.y, 0]);
  const toScaleX = interpolate(vRange, [
    sourceBBox.width / destinationBBox.width,
    1,
  ]);
  const toScaleY = interpolate(vRange, [
    sourceBBox.height / destinationBBox.height,
    1,
  ]);

  return (v) =>
    modalRenderer.set({
      opacity: v,
      x: toX(v),
      y: toY(v),
      scaleX: toScaleX(v),
      scaleY: toScaleY(v),
    });
}

function openModal(e) {
  if (e.target && e.target.classList.contains("modal-trigger")) {
    trigger = e.target;

    // Get bounding box of triggering element
    const triggerBBox = trigger.getBoundingClientRect();

    // Temporarily show modal container to measure modal
    dimmerRenderer.set("display", "block").render();
    modalContainerRenderer.set("display", "flex").render();
    modalRenderer.set("opacity", 0).render();

    // Get bounding box of final modal position
    const modalBBox = modal.getBoundingClientRect();

    // Get a function to tween the modal from the trigger
    const modalTweener = generateModalTweener(triggerBBox, modalBBox);

    // Fade in overlay
    tween({
      duration: 200,
      onUpdate: (v) => dimmerRenderer.set("opacity", v),
    }).start();

    chain([
      delay(75),
      tween({
        duration: 200,
        ease: easeOut,
        onUpdate: modalTweener,
      }),
    ]).start();
  }
}

function closeComplete() {
  isClosing = false;
  dimmerRenderer.set("display", "none").render();
  modalContainerRenderer.set("display", "none").render();
  modalRenderer.set({
    y: 0,
    scaleX: 1,
    scaleY: 1,
    transformOrigin: "50% 50%",
  });
}

function cancelModal(e) {
  if (e.target && e.target.classList.contains("cancel-modal") && !isClosing) {
    e.stopPropagation();
    isClosing = true;

    const triggerBBox = trigger.getBoundingClientRect();
    const modalBBox = modal.getBoundingClientRect();

    const modalTweener = generateModalTweener(triggerBBox, modalBBox);

    parallel([
      tween({
        from: dimmerRenderer.get("opacity"),
        to: 0,
        onUpdate: (v) => dimmerRenderer.set("opacity", v),
      }),
      tween({
        from: modalRenderer.get("opacity"),
        to: 0,
        duration: 250,
        onUpdate: modalTweener,
        onComplete: closeComplete,
      }),
    ]).start();
  }
}

function submitModal(e) {
  if (isClosing) return;
  e.stopPropagation();

  isClosing = true;

  const toScaleXIn = interpolate(vRange, [1, 1.2]);
  const toScaleYIn = interpolate(vRange, [1, 0.8]);

  const toScaleXOut = interpolate(vRange, [1.2, 0.5]);
  const toScaleYOut = interpolate(vRange, [0.8, 2]);

  chain([
    tween({
      onStart: () => modalRenderer.set("transform-origin", "50% 100%"),
      duration: 200,
      onUpdate: (v) =>
        modalRenderer.set({
          scaleX: toScaleXIn(v),
          scaleY: toScaleYIn(v),
          y: v * 100,
        }),
    }),
    parallel([
      tween({
        from: dimmerRenderer.get("opacity"),
        to: 0,
        onUpdate: (v) => dimmerRenderer.set("opacity", v),
      }),
      tween({
        onUpdate: (v) =>
          modalRenderer.set({
            opacity: 1 - v,
            scaleX: toScaleXOut(v),
            scaleY: toScaleYOut(v),
            y: -300 * easing.easeIn(v),
          }),
        duration: 200,
        onComplete: closeComplete,
      }),
    ]),
  ]).start();
}

document.addEventListener("click", openModal);
document.addEventListener("click", cancelModal);
document.querySelector(".submit").addEventListener("click", submitModal);

type Props = {
  show: boolean;
  trigger: any;
};

const Modal: FC<Props> = ({ show, trigger }) => {
  const modal = useRef(null);

  useEffect(() => {
    const triggerBBox = trigger.getBoundingClientRect();
    const modalBBox = modal.current.getBoundingClientRect();
  }, [show]);

  return (
    <>
      <div
        className={styles.overlay + " " + styles["cancel-modal"]}
        style={show ? { display: "block" } : {}}
      />
      <div
        className={styles["modal-container"] + " " + styles["cancel-modal"]}
        style={show ? { display: "flex" } : {}}
      >
        <div
          ref={modal}
          className={styles.modal}
          style={show ? { opacity: 0 } : {}}
        >
          <footer>
            <button className={styles.submit}>Ok</button>
            <button className={styles.cancel + " " + styles["cancel-modal"]}>
              No
            </button>
          </footer>
        </div>
      </div>
    </>
  );
};

export default Modal;
