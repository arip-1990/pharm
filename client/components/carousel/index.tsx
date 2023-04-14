import { FC } from "react";
import {
  CarouselProvider,
  Slider,
  Slide,
  ImageWithZoom,
} from "pure-react-carousel";

import "pure-react-carousel/dist/react-carousel.es.css";

interface Props extends HTMLElement {
  name: string;
  photos: { id: number; url: string }[];
}

const Carousel: FC<Props> = ({ name, photos }) => {
  return (
    <CarouselProvider
      visibleSlides={1}
      totalSlides={photos.length}
      step={1}
      naturalSlideWidth={320}
      naturalSlideHeight={320}
      hasMasterSpinner
    >
      <Slider>
        {photos.map((item, i) => (
          <Slide key={item.id} index={i}>
            <ImageWithZoom src={item.url} alt={name} />
          </Slide>
        ))}
      </Slider>
    </CarouselProvider>
  );
};

export { Carousel };
