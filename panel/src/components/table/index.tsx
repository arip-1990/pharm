import React from 'react';
import { SpinProps, Table as BaseTable, TablePaginationConfig} from 'antd';
import { ColumnProps } from 'antd/es/table';
import { FilterValue } from 'antd/lib/table/interface';

interface PropsType {
  columns: ColumnProps<any>[];
  data: any[] | undefined;
  loading: boolean | SpinProps;
  pagination: {current: number, total: number, pageSize: number};
  onChange: (pagination: TablePaginationConfig, filters: Record<string, FilterValue | null>, sorter: any) => void;
}

const Table: React.FC<PropsType> = ({columns, data, loading, pagination, onChange}) => {
  const handleChange = (pagination: TablePaginationConfig, filters: Record<string, FilterValue | null>, sorter: any) => {
    onChange(pagination, filters, sorter);
  }

  return (
    <BaseTable
      columns={columns}
      loading={loading}
      dataSource={data}
      onChange={handleChange}
      pagination={{...pagination}}
    />
  )
}

export default Table;
