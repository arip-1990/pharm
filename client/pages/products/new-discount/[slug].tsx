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
    const [combo, setCombo] = useState();

    const [products, setProducts] = useState([]);
    const [loading, setLoading] = useState(true);

    //console.log("d", products)

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
        () => [{ href: "/combo", text: "Комбо акция" }],
        []
    );

    return (
        <Layout title="Комбо акция" description="">
            <Breadcrumbs getDefaultGenerator={getDefaultGenerator} />
            <h1 className={styles_.title}>ВРЕМЯ ЗАЩИТИТЬСЯ ОТ ПРОСТУДЫ!</h1>

            <p>Период акции с 1 по 31 декабря.</p>
            <p>При покупке 2 РАЗНЫХ позиций из списка получите скидку -25% на комплект.</p>
            <p>*Акция «Время защититься от простуды» действует с 01.12.2025 г. по 31.12.2025 г. Скидки по акции не суммируются с другими скидками и акциями на товары. Представленная информация о товарах, их стоимости, характеристиках, фото ни при каких условиях не является публичной офертой. Количество акционного товара ограничено. Внешний вид упаковки может отличаться от изображения на рекламном макете. Организатор акции — ООО "Социальная аптека" (367010, РД, г. Махачкала, пр. Гамидова, д.48; ИНН 0571008484; ОГРН 1160571061353). Организатор вправе в одностороннем порядке изменить условия акции. Реклама.</p>

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







