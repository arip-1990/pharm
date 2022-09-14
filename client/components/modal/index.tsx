import { FC, ReactNode } from "react";
import { Modal as BaseModal } from "react-bootstrap";
import styles from "./Modal.module.scss";

interface Props {
  show: boolean;
  footer?: ReactNode;
}

const Modal: FC<Props> = ({ show, children, footer }) => {
  return (
    <BaseModal
      dialogClassName={styles.dialog}
      contentClassName={styles.modal}
      show={show}
      // onHide={handleHide}
      centered
    >
      <BaseModal.Body style={{ padding: "3rem" }}>{children}</BaseModal.Body>

      {footer ? (
        <BaseModal.Footer className="justify-content-center">
          {footer}
        </BaseModal.Footer>
      ) : null}
    </BaseModal>
  );
};

export default Modal;
