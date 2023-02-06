import { FC } from "react";
import Layout from "../templates";
import BaseProfile from "../templates/profile";
import { useAuth } from "../hooks/useAuth";
import { Table } from "../components/table";

const Profile: FC = () => {
  const { user } = useAuth();

  return (
    <Layout title="Профиль - Сеть аптек 120/80">
      <BaseProfile title="Персональные данные">
        <Table rounded striped>
          <tr>
            <td>Фамилия:</td>
            <td>{user?.lastName}</td>
          </tr>
          <tr>
            <td>Имя:</td>
            <td>{user?.firstName}</td>
          </tr>
          <tr>
            <td>Отчество:</td>
            <td>{user?.middleName}</td>
          </tr>
          <tr>
            <td>Дата рождения:</td>
            <td>{user?.birthDate?.format("DD.MM.Y")}</td>
          </tr>
          <tr>
            <td>Мобильный телефон:</td>
            <td>{user?.phone}</td>
          </tr>
          <tr>
            <td>Дата регистрации:</td>
            <td>{user?.registrationDate?.format("DD.MM.Y")}</td>
          </tr>
          <tr>
            <td>Магазины регистрации:</td>
            <td>{user?.orgUnitName}</td>
          </tr>
        </Table>
      </BaseProfile>
    </Layout>
  );
};

export default Profile;
