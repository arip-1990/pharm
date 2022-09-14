import { useCallback } from 'react';
import { CookieSetOptions } from 'universal-cookie';
import { useCookies } from 'react-cookie';
import { COOKIE_DOMAIN } from '../lib/api';

const useCookie = (cookieName: string): [string|null, (newValue: string, options?: CookieSetOptions) => void, () => void] => {
    const [cookies, setCookie, removeCookie] = useCookies([cookieName]);

  const updateCookie = useCallback((newValue: string, options?: CookieSetOptions) => {
    setCookie(cookieName, newValue, {...options, domain: COOKIE_DOMAIN});
    }, [cookieName]);

  const deleteCookie = useCallback((options?: CookieSetOptions) => {
    removeCookie(cookieName, options);
  }, [cookieName]);

  return [cookies[cookieName], updateCookie, deleteCookie];
};

export {useCookie};
