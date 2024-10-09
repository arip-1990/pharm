import moment from "moment";

interface user_likes {
    "id": string,
    "username": null | undefined | string,
    "first_name": string | null,
    "last_name": string | null,
    "middle_name": null | string,
    "phone": string,
    "email": null | string,
    "gender": number,
    "birth_date": string,
    "phone_verified_at": string,
    "email_verified_at": null | string,
    "token": null | string,
    "created_at": string,
    "updated_at": string,
    "deleted_at": null | any,
    "role_id": null | any,
    "pivot": {
        "photo_id": number,
        "user_id": string
    }
}

interface age_category {
    id: number,
    "created_at": string,
    "updated_at": string,
    "Age": string
}

export interface IPhotoKids {
    id: number;
    "created_at": string,
    "updated_at": string,
    "link": string,
    "photo_name": string,
    "birthdate": string,
    "first_name": string,
    "last_name": string,
    "middle_name": boolean | null | undefined,
    "published": boolean,
    "user_id": string,
    "age_category_id": number,
    "age_category": age_category
    "users_likes": user_likes[]
}


export interface ArrayPhotoId {
    id: number
}
