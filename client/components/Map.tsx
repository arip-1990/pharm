import { FC } from "react";
import {
  Map as BaseMap,
  YMaps,
  Clusterer,
  Placemark,
} from "@pbe/react-yandex-maps";

interface Props {
  center?: [number, number];
  points: {
    title: string;
    description: string;
    coordinates: [number, number];
  }[];
}

const Map: FC<Props> = ({ points, center = [42.961079, 47.534646] }) => {
  return (
    <YMaps
      query={{ apikey: "de8de84b-e8b4-46c9-ba10-4cf2911deebf", lang: "ru_RU" }}
    >
      <BaseMap
        width="100%"
        height={400}
        defaultState={{
          center,
          zoom: 11,
          behaviors: ["default", "scrollZoom"],
        }}
      >
        <Clusterer
          options={{
            preset: "islands#invertedVioletClusterIcons",
            groupByCoordinates: false,
            gridSize: 80,
          }}
        >
          {points.map((point, index) => (
            <Placemark
              key={index + 1}
              geometry={point.coordinates}
              properties={{
                balloonContentHeader: point.title,
                balloonContentBody: point.description,
              }}
              options={{ preset: "islands#violetIcon" }}
            />
          ))}
        </Clusterer>
      </BaseMap>
    </YMaps>
  );
};

export default Map;
