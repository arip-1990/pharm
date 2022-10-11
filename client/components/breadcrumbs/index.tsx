import { useRouter } from "next/router";
import { FC, HTMLAttributes, useMemo } from "react";
import Crumb from "./Crumb";

interface Props extends HTMLAttributes<HTMLElement> {
  getDefaultGenerator: () => {href: string, text: string}[];
}

const generatePathParts = (pathStr: string) => {
  const pathWithoutQuery = pathStr.split("?")[0];
  return pathWithoutQuery.split("/").filter((v) => v.length > 0);
};

const Breadcrumbs: FC<Props> = ({getDefaultGenerator}) => {
  // const router = useRouter();

  // const breadcrumbs = useMemo(() => {
  //   const asPathNestedRoutes = generatePathParts(router.asPath);
  //
  //   const crumbList = asPathNestedRoutes.map((path, idx) => {
  //     const href = "/" + asPathNestedRoutes.slice(0, idx + 1).join("/");
  //     return {
  //       href,
  //       text: getDefaultGenerator(),
  //     };
  //   });
  //
  //   return [{ href: "/", text: "Главная" }, ...crumbList];
  // }, [
  //   router.asPath,
  //   router.pathname,
  //   router.query,
  //   getDefaultGenerator,
  // ]);

  const breadcrumbs = useMemo(() => [{ href: "/", text: "Главная" }, ...getDefaultGenerator()], [getDefaultGenerator]);

  return (
    <nav aria-label="breadcrumb">
      <ol className="breadcrumb">
        {breadcrumbs.map((crumb, idx) => (
          <Crumb {...crumb} key={idx} last={idx === breadcrumbs.length - 1} />
        ))}
      </ol>
    </nav>
  );
};

export default Breadcrumbs;
