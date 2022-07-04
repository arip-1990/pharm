import { FC, MouseEvent, useEffect, useRef } from "react";

type Props = {
  src: string;
  alt?: string;
};

const Zoom: FC<Props> = ({ src, alt }) => {
  const zoomRef = useRef<HTMLElement>(null);

  const moveHandle = (event: MouseEvent<HTMLElement>) => {
    const zoomer = event.currentTarget;
    let offsetX = 0,
      offsetY = 0;
    event.offsetX
      ? (offsetX = event.offsetX)
      : (offsetX = event.touches[0].pageX);
    event.offsetY
      ? (offsetY = event.offsetY)
      : (offsetX = event.touches[0].pageX);
    zoomer.style.backgroundPosition =
      (offsetX / zoomer.offsetWidth) * 100 +
      "% " +
      (offsetY / zoomer.offsetHeight) * 100 +
      "%";
  };

  const leaveHandle = () =>
    (zoomRef.current.style =
      "background-image: " + zoomRef.current.style.backgroundImage);

  useEffect(() => {
    if (zoomRef.current) {
      zoomRef.current.addEventListener("mousemove", moveHandle);
      zoomRef.current.addEventListener("mouseleave", leaveHandle);
    }

    return () => {
      zoomRef.current?.removeEventListener("mousemove", moveHandle);
      zoomRef.current?.removeEventListener("mouseleave", leaveHandle);
    };
  }, []);

  return (
    <figure
      ref={zoomRef}
      className="zoom"
      style={{ backgroundImage: `url(${src})` }}
    >
      <img className="mw-100 m-auto" itemProp="image" src={src} alt={alt} />
    </figure>
  );
};

export default Zoom;
