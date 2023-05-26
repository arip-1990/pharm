import { useCallback, useEffect, useState } from "react";

const useDebounce = (timeout: number = 300) => {
  const [timer, setTimer] = useState<NodeJS.Timeout>();
  useEffect(() => () => clearTimeout(timer), [timeout]);

  return useCallback(
    (func: () => void) => setTimer(setTimeout(func, timeout)),
    [timeout]
  );
};

export { useDebounce };
