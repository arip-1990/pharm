import { useRouter } from "next/router";
import { FC, useEffect, useState } from "react";
import { useFormik } from "formik";
import Layout from "../../../components/layout";
import { useLocalStorage } from "react-use-storage";
import { ICart } from "../../../models/ICart";
import { Delivery, Payment } from "../../../components/checkout";
import { useMountedState } from "react-use";

const Checkout: FC = () => {
  const [carts] = useLocalStorage<ICart[]>("cart", []);
  const router = useRouter();
  const isMounted = useMountedState();
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

  const formik = useFormik({
    initialValues: {
      delivery: "0",
      payment: "0",
      city: undefined,
      street: undefined,
      house: undefined,
      entrance: undefined,
      floor: undefined,
      apt: undefined,
      service_to_door: undefined,
      rule: false,
    },
    onSubmit: (values) => {
      console.log(values);
    },
  });

  return (
    <Layout>
      <h1 className="text-center">Оформление заказа</h1>
      <form className="row row-cols-1 checkout" onSubmit={formik.handleSubmit}>
        <div className="col-md-8 p-4" style={{ border: "2px solid #f7f7f7" }}>
          <h4 className="text-center">Способ получения</h4>
          <Delivery
            recipe={recipe}
            defaultValue={formik.values.delivery}
            onChange={formik.handleChange}
            deliveryValues={formik.values}
          />

          <h4
            className="text-center p-4 mt-3"
            style={{ borderTop: "2px solid #f7f7f7" }}
          >
            Способ оплаты
          </h4>
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
                {/* <p
                  style={{ fontSize: "0.75rem", fontWeight: 300 }}
                  className="text-danger"
                >
                  Обязательно для заполнения.
                </p> */}
              </div>
            </div>
          </div>
        </div>
        <div className="col-md-4 p-4" style={{ border: "2px solid #f7f7f7" }}>
          <h4 className="text-center">Ваш заказ</h4>
          {isMounted() &&
            carts.map((item) => (
              <div key={item.product.id} className="row">
                <div className="col-6 col-md-8 offset-1 offset-md-0">
                  <a href="{{ route('catalog.product', ['product' => $item->product->slug]) }}">
                    {item.product.name}
                  </a>
                </div>
                <div className="col-4 col-md-4 text-end">
                  {item.product.minPrice}&#8381;
                  <br />
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
            <button className="btn btn-primary" type="submit">
              Подтвердить заказ
            </button>
          </div>
        </div>
      </form>
    </Layout>
  );
};

export default Checkout;
