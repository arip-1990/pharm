import React from "react";
import {
  Layout,
  Button,
  Card,
  Form,
  Input,
  Row,
  Checkbox,
  message,
} from "antd";
import { UserOutlined, LockOutlined } from "@ant-design/icons";
import { useAuth } from "../hooks/useAuth";

const Login: React.FC = () => {
  const [loading, setLoading] = React.useState<boolean>(false);
  const { login } = useAuth();

  const handleSubmit = async (values: any) => {
    setLoading(true);
    try {
      await login(values.email, values.password, values.remember);
    } catch (error) {
      // message.error(error?.response?.data.message);
      console.log(error);
    }
    setLoading(false);
  };

  return (
    <Layout style={{ minHeight: "100vh" }}>
      <Row style={{ margin: "auto" }}>
        <Card title="Вход" bordered={false} style={{ width: 300 }}>
          <Form name="login" onFinish={handleSubmit}>
            <Form.Item
              name="email"
              rules={[
                { required: true, message: "Пожалуйста, введите вашу почту!" },
              ]}
            >
              <Input prefix={<UserOutlined />} placeholder="Username" />
            </Form.Item>
            <Form.Item
              name="password"
              rules={[
                { required: true, message: "Пожалуйста, введите свой пароль!" },
              ]}
            >
              <Input
                prefix={<LockOutlined />}
                type="password"
                placeholder="Password"
              />
            </Form.Item>
            <Form.Item name="remember" valuePropName="checked">
              <Checkbox>Запомнить меня</Checkbox>
            </Form.Item>

            <Form.Item style={{ textAlign: "center" }}>
              <Button loading={loading} type="primary" htmlType="submit">
                Войти
              </Button>
            </Form.Item>
          </Form>
        </Card>
      </Row>
    </Layout>
  );
};

export default Login;
