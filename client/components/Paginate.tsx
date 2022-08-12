import { useRouter } from "next/router";
import { FC, useEffect, useState } from "react";
import ReactPaginate from "react-paginate";

type Props = {
  current: number;
  total: number;
  pageSize?: number;
};

const Paginate: FC<Props> = ({ current, total, pageSize = 12 }) => {
  const router = useRouter();
  const path = router.asPath.split("?")[0];
  const [pageCount, setPageCount] = useState(0);

  useEffect(() => {
    setPageCount(Math.ceil(total / pageSize));
  }, [pageSize]);

  const handlePageClick = ({ selected }) => {
    router.push(path + "?page=" + (selected + 1));
  };

  return (
    <ReactPaginate
      previousLabel="<"
      nextLabel=">"
      pageClassName="page-item"
      pageLinkClassName="page-link"
      previousClassName="page-item"
      previousLinkClassName="page-link"
      nextClassName="page-item"
      nextLinkClassName="page-link"
      breakLabel="..."
      breakClassName="page-item"
      breakLinkClassName="page-link"
      pageCount={pageCount}
      marginPagesDisplayed={2}
      pageRangeDisplayed={5}
      onPageChange={handlePageClick}
      containerClassName="pagination justify-content-center my-3"
      activeClassName="active"
      forcePage={current - 1}
    />
  );
};

export default Paginate;
