import { useRouter } from "next/router";
import { FC, useCallback, useEffect, useState } from "react";
import { FormikErrors, FormikHelpers, useFormik } from "formik";
import { useLocalStorage } from "react-use-storage";
import axios from "axios";

import Layout from "../../../templates";
import { ICart } from "../../../models/ICart";
import { Delivery, Payment } from "../../../components/checkout";
import { useNotification } from "../../../hooks/useNotification";
import { useMounted } from "../../../hooks/useMounted";
import { useCookie } from "../../../hooks/useCookie";
import api from "../../../lib/api";
import Breadcrumbs from "../../../components/breadcrumbs";
import Link from "next/link";

interface Values {
  delivery: number;
  payment: number;
  city?: string;
  street?: string;
  house?: string;
  entrance?: number;
  floor?: number;
  apt?: number;
  service_to_door: boolean;
  rule: boolean;
}

const ErrorField: FC<{ name: string; errors: FormikErrors<Values> }> = ({
  name,
  errors,
}) => {
  const style = {
    width: "100%",
    marginTop: "0.25rem",
    fontSize: "0.85rem",
    color: "#dc3545",
  };

  return errors[name] ? <div style={style}>{errors[name]}</div> : null;
};

const Checkout: FC = () => {
  const [carts] = useLocalStorage<ICart[]>("cart", []);
  const [city] = useCookie("city");
  const router = useRouter();
  const isMounted = useMounted();
  const notification = useNotification();
  const [recipe, setRecipe] = useState<boolean>(false);
  const [totalPrice, setTotalPrice] = useState<number>(0);
  const { slug } = router.query;

  useEffect(() => {
    let tmp = { recipe: false, totalPrice: 0 };
    carts.forEach((item) => {
      if (item.product.recipe) tmp.recipe = true;
      tmp.totalPrice += item.quantity * item.price;
    });

    setRecipe(tmp.recipe);
    setTotalPrice(tmp.totalPrice);
  }, [slug]);

  const getDefaultGenerator = useCallback(
    () => [
      { href: "/cart", text: "Корзина" },
      { href: "/cart/store", text: "Выбор аптеки" },
      { href: `/cart/checkout/${String(slug)}`, text: "Оформление заказа" },
    ],
    []
  );

  const formik = useFormik({
    initialValues: {
      delivery: 0,
      payment: 0,
      city: city,
      street: undefined,
      house: undefined,
      entrance: undefined,
      floor: undefined,
      apt: undefined,
      service_to_door: false,
      rule: false,
    },
    onSubmit: async (values: Values, actions: FormikHelpers<Values>) => {
      const items = carts.map((item) => ({
        id: item.product.id,
        price: item.price,
        quantity: item.quantity,
      }));
      try {
        const { data } = await api.post<{ id: number; paymentUrl?: string }>(
          "v1/order/checkout",
          {
            ...values,
            store: slug,
            price: totalPrice,
            items,
          }
        );

        if (data.paymentUrl) window.location.href = data.paymentUrl;
        else router.push(`/order/checkout/${data.id}/success`);
      } catch (error) {
        if (axios.isAxiosError(error)) {
          if (error.response.status === 422) {
            for (const [key, value] of Object.entries(
              error.response.data?.errors || {}
            )) {
              if (key in values) {
                actions.setFieldError(key, value[0]);
              }
            }
          } else notification("error", error.response.data.message);
        }
        console.log(error?.response.data);
      }
      actions.setSubmitting(false);
    },
  });

  return (
    <Layout title="Оформление заказа - Сеть аптек 120/80">
      <Breadcrumbs getDefaultGenerator={getDefaultGenerator} />

      <h5 className="text-center">Оформление заказа</h5>
      {recipe ? (
        <div className="alert alert-danger" role="alert">
          Заказать рецептурный препарат на сайте, можно только путем самовывоза
          из аптеки при наличии рецепта, выписанного врачом!
        </div>
      ) : null}

      <form className="row row-cols-1 checkout" onSubmit={formik.handleSubmit}>
        <div className="col-md-8 p-4" style={{ border: "2px solid #f7f7f7" }}>
          <h5 className="text-center">Способ получения</h5>
          <Delivery
            recipe={recipe}
            deliveryAvailabe={city?.toLowerCase().includes("махачкала")}
            defaultValue={formik.values.delivery}
            onChange={formik.handleChange}
            deliveryValues={formik.values}
            errors={formik.errors}
          />

          <h5
            className="text-center p-4 mt-3"
            style={{ borderTop: "2px solid #f7f7f7" }}
          >
            Способ оплаты
          </h5>
          <Payment
            defaultValue={formik.values.payment}
            onChange={formik.handleChange}
          />

          <div className="row my-3">
            <div className="col offset-1 offset-lg-0 offset-xl-1">
              <div className="form-check">
                <input
                  className="form-check-input"
                  type="checkbox"
                  name="rule"
                  id="rule"
                  checked={formik.values.rule}
                  onChange={formik.handleChange}
                />
                <label className="form-check-label" htmlFor="rule">
                  Я согласен(а) с правилами сайта
                </label>
                <ErrorField name="rule" errors={formik.errors} />
              </div>
            </div>
          </div>
        </div>

        <div className="col-md-4 p-4" style={{ border: "2px solid #f7f7f7" }}>
          <h5 className="text-center">Ваш заказ</h5>
          {isMounted() &&
            carts.map((item) => (
              <div key={item.product.id} className="row mb-3">
                <div className="col-6 col-md-8 offset-1 offset-md-0">
                  <Link href={`/catalog/product/${item.product.slug}`}>
                    <a>{item.product.name}</a>
                  </Link>
                </div>
                <div className="col-4 col-md-4 text-end">
                  {item.product.minPrice}&#8381;{" "}
                  <span className="text-muted">x {item.quantity}</span>
                </div>
              </div>
            ))}

          <div
            className="row py-3 mt-3"
            style={{ borderTop: "2px solid #f7f7f7" }}
          >
            <div className="col-5 col-md-6 col-lg-8 offset-1 offset-md-0">
              Стоимость:
            </div>
            <div className="col-5 col-md-6 col-lg-4 text-end">
              {totalPrice}&#8381;
            </div>
          </div>
          <div className="row">
            <div className="col-5 col-md-6 col-lg-8 offset-1 offset-md-0">
              Самовывоз:
            </div>
            <div className="col-5 col-md-6 col-lg-4 text-end">Бесплатно</div>
          </div>

          <div
            className="row py-3 mt-3"
            style={{ borderTop: "2px solid #f7f7f7" }}
          >
            <h5 className="col-5 col-md-6 col-lg-8 offset-1 offset-md-0">
              Итого:
            </h5>
            <h5 className="col-5 col-md-6 col-lg-4 text-end">
              {totalPrice}&#8381;
            </h5>
          </div>

          <div className="text-center">
            <button
              className="btn btn-primary"
              type="submit"
              disabled={formik.isSubmitting}
            >
              Подтвердить заказ
            </button>
          </div>
        </div>
      </form>
    </Layout>
  );
};

export default Checkout;
