import Link from "next/link";
import {FC, useCallback} from "react";
import { useLocalStorage } from "react-use-storage";
import Layout from "../components/layout";
import { IProduct } from "../models/IProduct";
import defaultImage from "../assets/images/default.png";
import { useMounted } from "../hooks/useMounted";
import Breadcrumbs from "../components/breadcrumbs";

const Favorite: FC = () => {
  const [favorites] = useLocalStorage<IProduct[]>("favorites", []);
  const isMounted = useMounted();

  const getDefaultGenerator = useCallback(() => [
    { href: '/favorite', text: "Избранное" }
  ], []);

  return (
    <Layout title="Избранное - Сеть аптек 120/80">
      <Breadcrumbs getDefaultGenerator={getDefaultGenerator} />

      <div className="row">
        <div className="col">
          <div className="card">
            <div className="card-header">Избранное</div>
            <div className="card-body">
              {isMounted() &&
                favorites.map((favorite) => {
                  <div key={favorite.id} className="row favorite">
                    <div className="col-3 col-md-3">
                      <img
                        alt=""
                        src={
                          favorite.photos.length
                            ? favorite.photos[0].url
                            : defaultImage.src
                        }
                        style={{ height: "120px", width: "120px" }}
                      />
                    </div>
                    <div className="col-7 col-md-8">
                      <Link href={`/product/${favorite.slug}`}>
                        <a className="favorite_title">{favorite.name}</a>
                      </Link>
                    </div>
                    <span className="col-2 col-md-1 favorite-remove"></span>
                  </div>;
                })}
            </div>
          </div>
        </div>
      </div>
    </Layout>
  );
};

export default Favorite;
