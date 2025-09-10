import React, {useCallback, useEffect, useState} from "react";
import Layout from "../../../templates";
import { useRouter } from "next/router";
import api from "../../../lib/api";
import Card from "../../../components/card";
import styles_ from "../../../components/Kids/Cards/gallery.module.css";
import Breadcrumbs from "../../../components/breadcrumbs";

const SpecialOffer = () => {
    const router = useRouter();
    const { id } = router.query;

    const [products, setProducts] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        if (!id) return; // ждём пока появится id

        const fetchProducts = async () => {
            try {
                setLoading(true);

                // Если id строка типа "12,44", превратим её в массив
                const ids = Array.isArray(id) ? id : id.split(",");

                const { data } = await api.get("v2/products/special-offer", {
                    params: { id: ids }, // axios сам соберёт ?id[]=12&id[]=44
                });

                setProducts(data.data); // Laravel Resource возвращает { data: [...] }
                console.log(data.data, "ddddddddddd")
            } catch (error) {
                console.error("Ошибка загрузки:", error);
            } finally {
                setLoading(false);
            }
        };

        fetchProducts();
    }, [id]);
    const getDefaultGenerator = useCallback(
        () => [{ href: "/special-offer", text: "Специальные предложения" }],
        []
    );
    return (
        <Layout title="Спец предложения" description="">
            <Breadcrumbs getDefaultGenerator={getDefaultGenerator} />
            <h1 className={styles_.title}>Специальные предложения</h1>

            {loading ? (
                <p>Загрузка...</p>
            ) : (
                <div className="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3">
                    {products.map((product) => (
                        <div key={product.id} className="col">
                            <Card product={product}/>
                        </div>
                    ))}
                </div>
            )}
        </Layout>
    );
};

export default SpecialOffer;
