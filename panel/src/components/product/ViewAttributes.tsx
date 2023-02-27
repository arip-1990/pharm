import React from "react";
import { Card, Input, Form, Space, Button, Modal, Select } from "antd";
import { EditOutlined, CheckOutlined, CloseOutlined } from "@ant-design/icons";
import { Table, Editor } from "..";
import { IProduct } from "../../models/IProduct";
import { useUpdateAttributesProductMutation } from "../../services/ProductService";
import { useFetchAttributesQuery } from "../../services/AttributeService";

interface PropsAttributeType {
  visible: boolean;
  attributes: any;
  onCreate: (values: number[]) => void;
  onCancel: () => void;
}

const NewAttribute: React.FC<PropsAttributeType> = ({
  visible,
  attributes,
  onCreate,
  onCancel,
}) => {
  const [form] = Form.useForm();

  const handleOk = async () => {
    try {
      const { attributes } = await form.validateFields();
      form.resetFields();
      onCreate(attributes);
    } catch (e) {
      console.log(e);
    }
  };

  return (
    <Modal
      title="Добавить аттрибут"
      okText="Добавить"
      visible={visible}
      onCancel={onCancel}
      onOk={handleOk}
    >
      <Form component={false} form={form}>
        <Form.Item style={{ margin: 0 }} name="attributes">
          <Select mode="multiple" allowClear style={{ width: "100%" }}>
            {attributes?.map((item: any) => (
              <Select.Option key={item.id} value={item.id}>
                {item.name}
              </Select.Option>
            ))}
          </Select>
        </Form.Item>
      </Form>
    </Modal>
  );
};

interface PropsType {
  product?: IProduct;
  loading: boolean;
}

const ViewAttributes: React.FC<PropsType> = ({ product, loading }) => {
  const [edit, setEdit] = React.useState<boolean>(false);
  const [showModal, setShowModal] = React.useState<boolean>(false);
  const [newAttributes, setNewAttributes] = React.useState<number[]>([]);
  const { data: attributes } = useFetchAttributesQuery();
  const [updateProduct] = useUpdateAttributesProductMutation();
  const [form] = Form.useForm();

  const columns = [
    { dataIndex: "key", width: 136 },
    { dataIndex: "type", colSpan: 0, render: () => undefined },
    {
      dataIndex: "value",
      render: (item: any) => {
        if (edit) {
          switch (item.type) {
            case "string":
              return (
                <Form.Item
                  style={{ margin: 0 }}
                  name={item.name}
                  initialValue={item.value}
                >
                  {item.variants.length ? (
                    <Select style={{ width: "100%" }}>
                      {item.variants.map((variant: string, i: number) => (
                        <Select.Option key={i + 1} value={variant}>
                          {variant}
                        </Select.Option>
                      ))}
                    </Select>
                  ) : (
                    <Input />
                  )}
                </Form.Item>
              );
            // case 'number':
            //     return <Form.Item style={{margin: 0}} name={item.name} initialValue={item.value}><InputNumber /></Form.Item>;
            case "text":
              return (
                <Form.Item
                  style={{ margin: 0 }}
                  name={item.name}
                  initialValue={item.value}
                >
                  <Editor />
                </Form.Item>
              );
          }
        }
        return item.type === "text" ? (
          <div dangerouslySetInnerHTML={{ __html: item.value || "" }} />
        ) : (
          item.value
        );
      },
    },
  ];

  const handleSave = async () => {
    try {
      let data = await form.validateFields();
      if (product) updateProduct({ slug: product.slug, data });
      setEdit(false);
      setNewAttributes([]);
    } catch (e) {
      console.log(e);
    }
  };

  const handleReset = () => {
    setEdit(false);
    form.resetFields();
    setNewAttributes([]);
  };

  const getData = () => {
    let data: any = [];
    let attrId: number[] = [];
    product?.attributes.forEach((item) => {
      attrId.push(item.id);
      data.push({
        key: item.name,
        type: item.type,
        value: item,
      });
    });
    attributes
      ?.filter(
        (item) =>
          !attrId.includes(item.id) &&
          (item.required || newAttributes.includes(item.id))
      )
      .forEach((item) => {
        data.push({
          key: item.name,
          type: item.type,
          value: item,
        });
      });

    return data;
  };

  return (
    <Card
      title="Аттрибуты"
      loading={loading}
      extra={
        edit ? (
          <Space>
            <CheckOutlined style={{ color: "#52c41a" }} onClick={handleSave} />
            <CloseOutlined style={{ color: "#ff4d4f" }} onClick={handleReset} />
          </Space>
        ) : (
          <EditOutlined
            style={{ color: "#1890ff" }}
            onClick={() => setEdit(true)}
          />
        )
      }
    >
      <Form component={false} form={form}>
        <Table
          size="small"
          showHeader={false}
          columns={columns}
          data={getData()}
          footer={
            edit
              ? () => (
                  <Button type="primary" onClick={() => setShowModal(true)}>
                    Добавить поле
                  </Button>
                )
              : undefined
          }
        />
      </Form>

      <NewAttribute
        visible={showModal}
        attributes={attributes?.filter(
          (item) =>
            !product?.attributes.some((item2) => item2.id === item.id) &&
            !item.required &&
            !newAttributes.includes(item.id)
        )}
        onCreate={(values) => {
          setNewAttributes(values);
          setShowModal(false);
        }}
        onCancel={() => setShowModal(false)}
      />
    </Card>
  );
};

export { ViewAttributes };
