import React, {FC, useState} from 'react';
import {DatePicker, Space, Radio, Input} from "antd";
const { RangePicker } = DatePicker;

interface FormExportProps{
  setFormDate:any
  setPlatform:any
  setValue:any
  value:any
}

const FormExport: FC<FormExportProps> = ({setFormDate, setPlatform, setValue, value}) => {


  const options = [
    { label: 'Все', value: 'all' },
    { label: 'Мобилка', value: 'mobile' },
    { label: 'Веб-сайт', value: 'web' },
  ];

  const getData = (date: any, dateString: any) => {
    setFormDate([{one:dateString[0], two:dateString[1]}])
  }


  const getPlatforms = (event: any) => {
    setPlatform(event.target.value)
  }

  const handleChange = (event:any) => {
    const inputValue = event.target.value;
    if (/^\d*$/.test(inputValue)) {
      setValue(inputValue);
    }
  }

  return (
    <div>
      <h5> Выберите дату! </h5>
      <Space direction="vertical" size={12}>
        <RangePicker
          onChange={getData}
        />
      </Space>

      <h5
        style={{marginTop: "25px",}}
      >
        Выберите необходимые заказы!
      </h5>

      <Radio.Group
        options={options}
        defaultValue="Все"
        optionType="button"
        buttonStyle="solid"
        onChange={getPlatforms}
      />

      <h5
        style={{marginTop: "25px",}}
      >
        Введите количество записей!
      </h5>
      <Input
        value={value}
        onChange={handleChange}
        placeholder="Введите только цифры"
        size='middle'
        style={{width: 290}}
      />

    </div>
  );
};

export default FormExport;
