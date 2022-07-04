import Layout from "../components/layout";
import React, { FC } from "react";
import Accordion from "../components/accordion";

const Test: FC = () => {
  return (
    <Layout banner>
      <Accordion>
        <Accordion.Item>
          <Accordion.Header>Test</Accordion.Header>
          <Accordion.Body>
            <p>
              Lorem ipsum dolor sit amet consectetur adipisicing elit. Sunt,
              unde. Repellat ratione excepturi saepe recusandae rerum.
              Voluptatem esse asperiores, vel eum velit ullam. Molestiae nihil
              voluptates vitae est quo. Ut?
            </p>
          </Accordion.Body>
        </Accordion.Item>
        <Accordion.Item>
          <Accordion.Header>Test</Accordion.Header>
          <Accordion.Body>
            <p>
              Lorem ipsum dolor sit amet consectetur adipisicing elit. Sunt,
              unde. Repellat ratione excepturi saepe recusandae rerum.
              Voluptatem esse asperiores, vel eum velit ullam. Molestiae nihil
              voluptates vitae est quo. Ut?
            </p>
          </Accordion.Body>
        </Accordion.Item>
        <Accordion.Item>
          <Accordion.Header>Test</Accordion.Header>
          <Accordion.Body>
            <p>
              Lorem ipsum dolor sit amet consectetur adipisicing elit. Sunt,
              unde. Repellat ratione excepturi saepe recusandae rerum.
              Voluptatem esse asperiores, vel eum velit ullam. Molestiae nihil
              voluptates vitae est quo. Ut?
            </p>
          </Accordion.Body>
        </Accordion.Item>
      </Accordion>
    </Layout>
  );
};

export default Test;
