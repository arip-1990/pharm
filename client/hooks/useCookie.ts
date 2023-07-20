import { useCallback } from 'react';
import { useCookies } from 'react-cookie';

import { COOKIE_DOMAIN } from '../lib/api';

const useCookie = (cookieName: string): [string, (newValue: string) => void, () => void] => {
    const [cookies, setCookie, removeCookie] = useCookies([cookieName]);

  const updateCookie = useCallback((newValue: string) => {
    setCookie(cookieName, newValue, {domain: COOKIE_DOMAIN, path: '/'});
    }, [cookieName]);

  const deleteCookie = useCallback(() => {
    removeCookie(cookieName);
  }, [cookieName]);

  return [cookies[cookieName], updateCookie, deleteCookie];
};

export {useCookie};
