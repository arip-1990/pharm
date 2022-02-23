import React from 'react';
import { Layout, Button, Card, Form, Input, Row, Checkbox, message } from 'antd'
import { UserOutlined, LockOutlined } from '@ant-design/icons'
import { useSanctum } from 'react-sanctum';
import { useNavigate } from 'react-router-dom';

const Login: React.FC = () => {
  const [loading, setLoading] = React.useState<boolean>(false);
  const { signIn } = useSanctum();
  const navigate = useNavigate();

  const handleSubmit = async (values: any) => {
    setLoading(true);
    try {
      await signIn(values.email, values.password, values.remember);
      navigate('/');
    }
    catch (e: any) {
      message.error(e.response.data.message);
    }
    finally {
      setLoading(false);
    }
  }

  return (
    <Layout style={{minHeight: '100vh'}}>
      <Row style={{margin: 'auto'}}>
    <Card title="Вход" bordered={false} style={{ width: 300 }}>
    <Form
      name="login"
      onFinish={handleSubmit}
    >
      <Form.Item
        name="email"
        rules={[{ required: true, message: 'Пожалуйста, введите вашу почту!' }]}
      >
        <Input prefix={<UserOutlined />} placeholder="Username" />
      </Form.Item>
      <Form.Item
        name="password"
        rules={[{ required: true, message: 'Пожалуйста, введите свой пароль!' }]}
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

      <Form.Item style={{textAlign: 'center'}}>
        <Button loading={loading} type="primary" htmlType="submit">
          Войти
        </Button>
      </Form.Item>
    </Form>
    </Card>
    </Row>
    </Layout>
  )
}

export default Login
