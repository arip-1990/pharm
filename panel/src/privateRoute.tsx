import { Route } from "react-router-dom";
import Layout from "./loayouts";
import Main from "./pages/Main";
import Order from "./pages/order";
import OrderView from "./pages/order/View";
import Product from "./pages/product";
import ProductView from "./pages/product/View";
import Offer from "./pages/offer";
import OfferView from "./pages/offer/View";

export default () => (
    <Route path='/' element={<Layout />}>
        <Route index element={<Main />} />
        <Route path='order'>
            <Route index element={<Order />} />
            <Route path=':id' element={<OrderView />} />
        </Route>
        <Route path='offer'>
            <Route index element={<Offer />} />
            <Route path=':slug' element={<OfferView />} />
        </Route>
        <Route path='product'>
            <Route index element={<Product />} />
            <Route path=':slug' element={<ProductView />} />
        </Route>
    </Route>
);
