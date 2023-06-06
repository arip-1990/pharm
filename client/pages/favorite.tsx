import Link from "next/link";
import { FC, useCallback } from "react";
import { useLocalStorage } from "react-use-storage";

import Layout from "../templates";
import { IProduct } from "../models/IProduct";
import defaultImage from "../assets/images/default.png";
import { useMounted } from "../hooks/useMounted";
import Breadcrumbs from "../components/breadcrumbs";
import {rgba} from "style-value-types";
import Card from "../components/card/index";
import favorite from "../components/card/Favorite";
import Pagination from "../components/pagination";

type Props = {
  product: IProduct;
};

const Favorite: FC = () => {
  const [favorites] = useLocalStorage<IProduct[]>("favorites", []);
  console.log(...favorites)


  const isMounted = useMounted();

  const getDefaultGenerator = useCallback(() => [
    { href: '/favorite', text: "Избранное" }
  ], []);

  return (

    <Layout title="Избранное - Сеть аптек 120/80">
      <Breadcrumbs getDefaultGenerator={getDefaultGenerator} />

      <div className="row">
        <div className="col-md-12 mt-3 mt-md-0">
          {favorites? (
              <>
                <div
                    className="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-lg-4 g-3 g-lg-4"
                    itemScope
                    itemType="https://schema.org/ItemList"
                >
                  <link itemProp="url" href="/catalog" />
                  {favorites.map((product) => (
                      <div key={product.id} className="col-10 offset-1 offset-sm-0">
                        <Card product={product} />
                      </div>
                  ))}
                </div>
                <div className="row mt-3">

                </div>
              </>
          ) : (
              <h3 className="text-center">Товары отсутствуют</h3>
          )}
        </div>
      </div>
      {/*<Breadcrumbs getDefaultGenerator={getDefaultGenerator} />*/}
      {/*  { isMounted && favorites.map((ev) => {return(*/}
      {/*      <div style={{display:"flex", justifyContent:"center", alignItems:"center"}}>*/}

      {/*        <div className="col-10 offset-1 offset-sm-0">*/}
      {/*          <Card product={ev}/>*/}
      {/*        </div>*/}
      {/*      </div>*/}
      {/*  )})}*/}




      {/*<div className="row">*/}
      {/*  <div className="col">*/}
      {/*    <div className="card">*/}
      {/*      <div className="card-header">Избранное</div>*/}
      {/*      <div className="card-body">*/}
      {/*        */}




      {/*        {favorites.map((favorite) => {*/}
      {/*            <div> {favorite.name} </div>*/}

      {/*            <div key={favorite.id} className="row favorite">*/}
      {/*              <div className="col-3 col-md-3">*/}
      {/*                <img*/}
      {/*                  alt=""*/}
      {/*                  src={*/}
      {/*                    favorite.photos.length*/}
      {/*                      ? favorite.photos[0].url*/}
      {/*                      : defaultImage.src*/}
      {/*                  }*/}
      {/*                  style={{ height: "120px", width: "120px" }}*/}
      {/*                />*/}
      {/*              </div>*/}
      {/*              <div className="col-7 col-md-8">*/}
      {/*                <Link href={`/product/${favorite.slug}`}>*/}
      {/*                  <a className="favorite_title">{favorite.name}</a>*/}
      {/*                </Link>*/}
      {/*              </div>*/}
      {/*              <span className="col-2 col-md-1 favorite-remove"></span>*/}
      {/*            </div>;*/}
      {/*          })}*/}
      {/*      </div>*/}
      {/*    </div>*/}
      {/*  </div>*/}
      {/*</div>*/}
    </Layout>
  );
};

export default Favorite;
