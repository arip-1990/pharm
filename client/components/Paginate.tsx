import classNames from "classnames";
import Link from "next/link";
import { useRouter } from "next/router";
import { FC } from "react";

type Props = {
  current: number;
  total: number;
  pageSize?: number;
};

type Pag = {
  totalPages: number;
  startPage: number;
  endPage: number;
  pages: number[];
};

const paginate = (
  totalItems: number,
  currentPage: number = 1,
  pageSize: number = 12,
  maxPages: number = 5
): Pag => {
  // calculate total pages
  let totalPages = Math.ceil(totalItems / pageSize);

  // ensure current page isn't out of range
  if (currentPage < 1) {
    currentPage = 1;
  } else if (currentPage > totalPages) {
    currentPage = totalPages;
  }

  let startPage: number, endPage: number;
  if (totalPages <= maxPages) {
    // total pages less than max so show all pages
    startPage = 1;
    endPage = totalPages;
  } else {
    // total pages more than max so calculate start and end pages
    let maxPagesBeforeCurrentPage = Math.floor(maxPages / 2);
    let maxPagesAfterCurrentPage = Math.ceil(maxPages / 2) - 1;
    if (currentPage <= maxPagesBeforeCurrentPage) {
      // current page near the start
      startPage = 1;
      endPage = maxPages;
    } else if (currentPage + maxPagesAfterCurrentPage >= totalPages) {
      // current page near the end
      startPage = totalPages - maxPages + 1;
      endPage = totalPages;
    } else {
      // current page somewhere in the middle
      startPage = currentPage - maxPagesBeforeCurrentPage;
      endPage = currentPage + maxPagesAfterCurrentPage;
    }
  }

  // calculate start and end item indexes
  let startIndex = (currentPage - 1) * pageSize;
  let endIndex = Math.min(startIndex + pageSize - 1, totalItems - 1);

  // create an array of pages to ng-repeat in the pager control
  let pages = Array.from(Array(endPage + 1 - startPage).keys()).map(
    (i) => startPage + i
  );

  // return object with all pager properties required by the view
  return {
    totalPages: totalPages,
    startPage: startPage,
    endPage: endPage,
    pages: pages,
  };
};

const Paginate: FC<Props> = ({ current, total, pageSize = 12 }) => {
  const router = useRouter();
  const pages = paginate(total, current, pageSize);
  const path = router.asPath.split("?")[0];

  return pages && pages.endPage > 1 ? (
    <nav>
      <ul className="pagination justify-content-center">
        <li className={classNames("page-item", { disabled: current == 1 })}>
          <Link href={`${path}?page=${current - 1 || 1}`}>
            <a className="page-link" aria-label="Previous">
              <span aria-hidden="true">&laquo;</span>
            </a>
          </Link>
        </li>
        {pages.pages.map((item) => (
          <li
            key={item}
            className={classNames("page-item", { active: current == item })}
          >
            <Link href={`${path}?page=${item}`}>
              <a className="page-link" aria-label="Previous">
                <span aria-hidden="true">{item}</span>
              </a>
            </Link>
          </li>
        ))}
        <li
          className={classNames("page-item", {
            disabled: current == pages.totalPages,
          })}
        >
          <Link href={`${path}?page=${current + 1}`}>
            <a className="page-link" aria-label="Next">
              <span aria-hidden="true">&raquo;</span>
            </a>
          </Link>
        </li>
      </ul>
    </nav>
  ) : null;
};

export default Paginate;
