import React from "react";
import { SpinProps, Table as BaseTable, TablePaginationConfig } from "antd";
import { ColumnProps } from "antd/es/table";
import { FilterValue } from "antd/lib/table/interface";
import { SizeType } from "antd/es/config-provider/SizeContext";
import { GetComponentProps, PanelRender } from "rc-table/lib/interface";

interface PropsType {
  size?: SizeType;
  showHeader?: boolean;
  columns: ColumnProps<any>[];
  data: any[] | undefined;
  loading?: boolean | SpinProps;
  pagination?: {
    current: number;
    total: number;
    pageSize: number;
    showQuickJumper?: boolean;
  };
  onChange?: (
    pagination: TablePaginationConfig,
    filters: Record<string, FilterValue | null>,
    sorter: any
  ) => void;
  onRow?: GetComponentProps<any>;
  footer?: PanelRender<any>;
}

const Table: React.FC<PropsType> = ({
  size,
  showHeader,
  columns,
  data,
  loading,
  pagination,
  onChange,
  onRow,
  footer,
}) => {
  const handleChange = (
    pagination: TablePaginationConfig,
    filters: Record<string, FilterValue | null>,
    sorter: any
  ) => {
    if (onChange) onChange(pagination, filters, sorter);
  };

  return (
    <BaseTable
      size={size}
      showHeader={showHeader}
      columns={columns}
      loading={loading}
      dataSource={data}
      onChange={handleChange}
      pagination={pagination ? { ...pagination } : false}
      onRow={onRow}
      footer={footer}
    />
  );
};

export default Table;
