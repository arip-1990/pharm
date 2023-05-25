import classNames from "classnames";
import { useRouter } from "next/router";
import { FC } from "react";

import { DOTS, usePagination } from "../../hooks/usePaginate";

type Props = {
  totalCount?: number;
  siblingCount?: number;
  currentPage?: number;
  pageSize?: number;
  className?: string;
  onPageChange?: (page: number | string) => void;
};

const Pagination: FC<Props> = (props) => {
  const {
    onPageChange,
    totalCount = 0,
    siblingCount = 1,
    currentPage = 0,
    pageSize = 15,
    className,
  } = props;
  const router = useRouter();

  const paginationRange = usePagination({
    currentPage,
    totalCount,
    siblingCount,
    pageSize,
  });

  if (currentPage === 0 || paginationRange.length < 2) {
    return null;
  }

  const handlePage = (page: any) => {
    let url = router.asPath;
    if (page === 1) url = url.replace(/[?&]page=\d+/i, "");
    else if (url.includes("page="))
      url = url.replace(/page=\d+/i, `page=${page}`);
    else
      url =
        url.split("?").length > 1
          ? (url += `&page=${page}`)
          : (url += `?page=${page}`);
    router.push(url);
  };

  let lastPage = paginationRange[paginationRange.length - 1];

  const onNext = () => {
    if (currentPage < +lastPage) handlePage(currentPage + 1);
  };

  const onPrevious = () => {
    if (currentPage > 1) handlePage(currentPage - 1);
  };

  return (
    <ul
      className={classNames("pagination justify-content-center my-3", {
        [className]: className,
      })}
    >
      <li
        className={classNames("page-item", {
          disabled: currentPage === 1,
        })}
        onClick={onPrevious}
      >
        <a
          role="button"
          className="page-link"
          tabIndex={1 === currentPage ? -1 : 0}
        >
          {"<"}
        </a>
      </li>
      {paginationRange.map((pageNumber, i) => {
        if (pageNumber === DOTS) {
          return (
            <li
              key={"dots" + i}
              className="page-item"
            // onClick={() => handlePage(currentPage + 5)}
            >
              <a role="button" className="page-link" tabIndex={0}>
                &#8230;
              </a>
            </li>
          );
        }

        return (
          <li
            key={pageNumber}
            className={classNames("page-item", {
              active: pageNumber === currentPage,
            })}
            onClick={() => {
              if (pageNumber !== currentPage) handlePage(pageNumber);
            }}
          >
            <a
              role="button"
              className="page-link"
              tabIndex={pageNumber === currentPage ? -1 : 0}
            >
              {pageNumber}
            </a>
          </li>
        );
      })}
      <li
        className={classNames("page-item", {
          disabled: currentPage === lastPage,
        })}
        onClick={onNext}
      >
        <a
          role="button"
          className="page-link"
          tabIndex={lastPage === currentPage ? -1 : 0}
        >
          {">"}
        </a>
      </li>
    </ul>
  );
};

export default Pagination;
