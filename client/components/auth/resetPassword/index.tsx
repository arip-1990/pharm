import { FC, useState } from "react";
import ChangePassword from "./ChangePassword";
import RequestPassword from "./RequestPassword";
import TempPassword from "./TempPassword";

type Props = {
  onSubmit: (success: boolean, verifyPhone?: boolean) => void;
};

const SetPassword: FC<Props> = ({ onSubmit }) => {
  const [type, setType] = useState<"request" | "temp" | "change">("request");

  const handleRequestPassword = (
    success: boolean,
    verifyPhone: boolean = false
  ) => {
    if (verifyPhone) onSubmit(false, true);
    else if (success) setType("temp");
  };

  const handleTempPassword = (success: boolean) => {
    if (success) setType("change");
  };

  const handleChangePassword = (success: boolean) => {
    if (success) {
      setType("change");
      onSubmit(true);
    }
  };

  return (
    <div>
      <h2 className="text-center mb-4">Сброс пароля</h2>
      {type === "temp" ? (
        <TempPassword onSubmit={handleTempPassword} />
      ) : type === "change" ? (
        <ChangePassword onSubmit={handleChangePassword} />
      ) : (
        <RequestPassword onSubmit={handleRequestPassword} />
      )}
    </div>
  );
};

export default SetPassword;
