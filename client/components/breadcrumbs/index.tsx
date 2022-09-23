import { useRouter } from "next/router";
import { FC, HTMLAttributes, useMemo } from "react";
import Crumb from "./Crumb";

interface Props extends HTMLAttributes<HTMLElement> {
  getTextGenerator?: (param: string, query: any) => null;
  getDefaultTextGenerator?: (path?: string) => string;
}

const _defaultGetTextGenerator = (param: string, query: any) => null;
const _defaultGetDefaultTextGenerator = (path: string) => path;

const generatePathParts = (pathStr: string) => {
  const pathWithoutQuery = pathStr.split("?")[0];
  return pathWithoutQuery.split("/").filter((v) => v.length > 0);
};

const Breadcrumbs: FC<Props> = ({
  getTextGenerator = _defaultGetTextGenerator,
  getDefaultTextGenerator = _defaultGetDefaultTextGenerator,
}) => {
  const router = useRouter();

  const breadcrumbs = useMemo(() => {
    const asPathNestedRoutes = generatePathParts(router.asPath);
    const pathnameNestedRoutes = generatePathParts(router.pathname);

    const crumbList = asPathNestedRoutes.map((path, idx) => {
      const param = pathnameNestedRoutes[idx].replace("[", "").replace("]", "");

      const href = "/" + asPathNestedRoutes.slice(0, idx + 1).join("/");
      return {
        href,
        textGenerator: getTextGenerator(param, router.query),
        text: getDefaultTextGenerator(path),
      };
    });

    return [{ href: "/", text: "Главная" }, ...crumbList];
  }, [
    router.asPath,
    router.pathname,
    router.query,
    getTextGenerator,
    getDefaultTextGenerator,
  ]);

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
