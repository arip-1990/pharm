export interface IAttribute {
  id: number;
  name: string;
  type: string;
  default: string|null;
  required: boolean;
  variants: string[];
  category: { id: string, name: string } | null;
}
