import { useRouter } from "next/router";
import { FC, useEffect } from "react";
import Layout from "../components/layout";
import BaseProfile from "../components/profile";
import { useAuth } from "../hooks/useAuth";

const Profile: FC = () => {
  const { isAuth, user } = useAuth();
  const router = useRouter();

  console.log(user);

  //   useEffect(() => {
  //     if (!isAuth) {
  //       router.back();
  //     }
  //   }, [user]);

  return (
    <Layout>
      <BaseProfile title="Персональные данные"></BaseProfile>
    </Layout>
  );
};

export default Profile;
