import {FC, useCallback} from "react";
import Layout from "../components/layout";
import BaseProfile from "../components/profile";
import { useAuth } from "../hooks/useAuth";
import Breadcrumbs from "../components/breadcrumbs";

const Profile: FC = () => {
  const { user } = useAuth();

  const getDefaultTextGenerator = useCallback((path: string) => ({ profile: "Профиль" }[path]), []);

  return (
    <Layout>
      <Breadcrumbs getDefaultTextGenerator={getDefaultTextGenerator} />

      <BaseProfile title="Персональные данные">
        <div className="row">
          <div
            className="col-12"
            style={{ fontSize: "1.2rem", lineHeight: 1.75 }}
          >
            <span style={{ fontWeight: 600 }}>Фамилия:</span> {user?.lastName}
          </div>
          <div
            className="col-12"
            style={{ fontSize: "1.2rem", lineHeight: 1.75 }}
          >
            <span>Имя:</span> {user?.firstName}
          </div>
          <div
            className="col-12"
            style={{ fontSize: "1.2rem", lineHeight: 1.75 }}
          >
            <span style={{ fontWeight: 600 }}>Отчество:</span>{" "}
            {user?.middleName}
          </div>
          <div
            className="col-12"
            style={{ fontSize: "1.2rem", lineHeight: 1.75 }}
          >
            <span style={{ fontWeight: 600 }}>Дата рождения:</span>{" "}
            {user?.birthDate?.format("DD.MM.Y")}
          </div>
          <div
            className="col-12"
            style={{ fontSize: "1.2rem", lineHeight: 1.75 }}
          >
            <span style={{ fontWeight: 600 }}>Мобильный телефон:</span>{" "}
            {user?.phone}
          </div>
          <div
            className="col-12"
            style={{ fontSize: "1.2rem", lineHeight: 1.75 }}
          >
            <span style={{ fontWeight: 600 }}>Дата регистрации:</span>{" "}
            {user?.registrationDate?.format("DD.MM.Y")}
          </div>
          <div
            className="col-12"
            style={{ fontSize: "1.2rem", lineHeight: 1.75 }}
          >
            <span style={{ fontWeight: 600 }}>Магазины регистрации:</span>{" "}
            {user?.orgUnitName}
          </div>
        </div>
      </BaseProfile>
    </Layout>
  );
};

export default Profile;
