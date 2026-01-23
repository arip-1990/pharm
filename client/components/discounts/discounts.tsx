import {ICart, IsComboValid} from "../../models/ICart";
import React from "react";

const getTextDiscount = (cart: ICart, title:string, isComboValid: IsComboValid|null): string | any => {

    if (cart.product.combo) {

        switch (title) {
            case "topDiscount":
                if (cart.product.combo) {
                    if (cart.product.discount > 0) {
                        return <h6>комбо скидка {cart.product.discount}% по условию</h6>
                    }else if (cart.product.discountPrice) {
                        return `Скидка ${cart.product.rubles}р за ${cart.product.quantity} шт `
                    }else {
                        return ""
                    }
                }else{
                    return ""
                }
            case "oldPrice":
                if (cart.product.minPrice != cart.product.discountPrice && cart.product.discountPrice != 0) {
                    return <>{cart.product.minPrice} &#8381;</>
                }else{
                    return "ffff"
                }
        }
    }else{
        switch (title) {
            case "topDiscount":
                if (cart.product.discount > 0) {
                    return `скидка ${cart.product.discount}%`
                }else if (cart.product.discountPrice) {
                    return `Скидка ${cart.product.rubles}р за ${cart.product.quantity} шт `
                }else {
                    return ""
                }
            case "oldPrice":
                if (cart.product.minPrice != cart.product.discountPrice && cart.product.discountPrice != 0) {
                    return <>{cart.product.minPrice} &#8381;</>
                }else{
                    return ""
                }
            case "oldPriceStyle":
                if (cart.product.discountPrice) {
                    return {textDecoration: 'line-through', color: 'red'}
                }else{
                    return {}
                }
            case "currentPriceWithDiscount":
                if (cart.product.discountPrice > 0 && cart.product.discountPrice != cart.product.minPrice) {
                    return <span> от {cart.product.discountPrice} &#8381; </span>
                }else {
                    return <span> от {cart.product.minPrice} &#8381; </span>
                }
        }
    }
}

export default getTextDiscount



