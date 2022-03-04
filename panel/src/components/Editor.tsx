import React from "react";
import { EditorState, convertFromHTML, ContentState } from 'draft-js';
import { Editor as BaseEditor } from 'react-draft-wysiwyg';
import { convertToHTML } from 'draft-convert';

interface PropsType {
    toolbar?: boolean;
    value?: string,
    onChange?: (value: string) => void
}

const Editor = ({toolbar, value, onChange}: PropsType) => {
    const [editorState, setEditorState] = React.useState(() => {
        const blocksFromHTML = convertFromHTML(value || '');
        const contentState = ContentState.createFromBlockArray(blocksFromHTML.contentBlocks, blocksFromHTML.entityMap);

        return EditorState.createWithContent(contentState);
    });

    const options = ['inline', 'blockType', 'list', 'link', 'embedded', 'emoji'];

    const handleChange = (value: EditorState) => {
        setEditorState(value);
        onChange && onChange(convertToHTML(editorState.getCurrentContent()));
    }

    return <BaseEditor editorClassName="ant-input" toolbarHidden={!toolbar} toolbar={{options}} editorState={editorState} onEditorStateChange={handleChange} localization={{locale: 'ru'}} />;
}

export default Editor;
