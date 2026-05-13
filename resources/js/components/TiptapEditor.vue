<script setup lang="ts">
import Color from '@tiptap/extension-color';
import Highlight from '@tiptap/extension-highlight';
import Image from '@tiptap/extension-image';
import Link from '@tiptap/extension-link';
import Placeholder from '@tiptap/extension-placeholder';
import TextAlign from '@tiptap/extension-text-align';
import { TextStyle } from '@tiptap/extension-text-style';
import Underline from '@tiptap/extension-underline';
import StarterKit from '@tiptap/starter-kit';
import { useEditor, EditorContent } from '@tiptap/vue-3';

import {
    Bold,
    Italic,
    Underline as UnderlineIcon,
    Heading2,
    List,
    ListOrdered,
    Quote,
    Undo,
    Redo,
    Image as ImageIcon,
    Link as LinkIcon,
    Highlighter,
} from 'lucide-vue-next';

import { watch, onBeforeUnmount } from 'vue';

interface Props {
    modelValue: string;
    placeholder?: string;
    minHeight?: string;
}

const props = withDefaults(defineProps<Props>(), {
    placeholder: 'Escriu aquí…',
    minHeight: '180px',
});

const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
}>();

const editor = useEditor({
    content: props.modelValue,

    extensions: [
        StarterKit,

        Placeholder.configure({
            placeholder: props.placeholder,
        }),

        Image.configure({
            inline: false,
            allowBase64: true,
        }),

        Link.configure({
            openOnClick: false,
        }),

        Underline,

        Highlight,

        TextStyle,

        Color,

        TextAlign.configure({
            types: ['heading', 'paragraph'],
        }),
    ],

    editorProps: {
        attributes: {
            class: 'prose prose-sm max-w-none focus:outline-none p-4 min-h-[180px]',
        },
    },

    onUpdate: ({ editor }) => {
        emit('update:modelValue', editor.getHTML());
    },
});

watch(
    () => props.modelValue,
    (val) => {
        if (editor.value && val !== editor.value.getHTML()) {
            editor.value.commands.setContent(val ?? '', {
                emitUpdate: false,
            });
        }
    },
);

onBeforeUnmount(() => {
    editor.value?.destroy();
});

function tbBtn(active: boolean) {
    return [
        'rounded-md p-2 transition',
        active
            ? 'bg-blue-100 text-blue-700'
            : 'text-gray-600 hover:bg-gray-100',
    ];
}

function addImage() {
    const url = window.prompt('Image URL');

    if (url) {
        editor.value?.chain().focus().setImage({ src: url }).run();
    }
}

function setLink() {
    const previousUrl = editor.value?.getAttributes('link').href;
    const url = window.prompt('URL', previousUrl);

    if (url === null) {
return;
}

    if (url === '') {
        editor.value?.chain().focus().unsetLink().run();

        return;
    }

    editor.value?.chain().focus().setLink({ href: url }).run();
}
</script>

<template>
    <div class="overflow-hidden rounded-xl border bg-white shadow-sm">
        <!-- Toolbar -->
        <div class="flex flex-wrap items-center gap-1 border-b bg-gray-50 p-2">
            <!-- Bold -->
            <button
                type="button"
                :class="tbBtn(editor?.isActive('bold') ?? false)"
                @click="editor?.chain().focus().toggleBold().run()"
            >
                <Bold class="h-4 w-4" />
            </button>

            <!-- Italic -->
            <button
                type="button"
                :class="tbBtn(editor?.isActive('italic') ?? false)"
                @click="editor?.chain().focus().toggleItalic().run()"
            >
                <Italic class="h-4 w-4" />
            </button>

            <!-- Underline -->
            <button
                type="button"
                :class="tbBtn(editor?.isActive('underline') ?? false)"
                @click="editor?.chain().focus().toggleUnderline().run()"
            >
                <UnderlineIcon class="h-4 w-4" />
            </button>

            <!-- Heading -->
            <button
                type="button"
                :class="
                    tbBtn(editor?.isActive('heading', { level: 2 }) ?? false)
                "
                @click="
                    editor?.chain().focus().toggleHeading({ level: 2 }).run()
                "
            >
                <Heading2 class="h-4 w-4" />
            </button>

            <!-- Bullet -->
            <button
                type="button"
                :class="tbBtn(editor?.isActive('bulletList') ?? false)"
                @click="editor?.chain().focus().toggleBulletList().run()"
            >
                <List class="h-4 w-4" />
            </button>

            <!-- Ordered -->
            <button
                type="button"
                :class="tbBtn(editor?.isActive('orderedList') ?? false)"
                @click="editor?.chain().focus().toggleOrderedList().run()"
            >
                <ListOrdered class="h-4 w-4" />
            </button>

            <!-- Quote -->
            <button
                type="button"
                :class="tbBtn(editor?.isActive('blockquote') ?? false)"
                @click="editor?.chain().focus().toggleBlockquote().run()"
            >
                <Quote class="h-4 w-4" />
            </button>

            <!-- Highlight -->
            <button
                type="button"
                :class="tbBtn(editor?.isActive('highlight') ?? false)"
                @click="editor?.chain().focus().toggleHighlight().run()"
            >
                <Highlighter class="h-4 w-4" />
            </button>

            <div class="mx-2 h-6 w-px bg-gray-300" />

            <!-- Link -->
            <button
                type="button"
                :class="tbBtn(editor?.isActive('link') ?? false)"
                @click="setLink"
            >
                <LinkIcon class="h-4 w-4" />
            </button>

            <!-- Image -->
            <button
                type="button"
                class="rounded-md p-2 text-gray-600 hover:bg-gray-100"
                @click="addImage"
            >
                <ImageIcon class="h-4 w-4" />
            </button>

            <div class="mx-2 h-6 w-px bg-gray-300" />

            <!-- Undo -->
            <button
                type="button"
                class="rounded-md p-2 text-gray-600 hover:bg-gray-100"
                @click="editor?.chain().focus().undo().run()"
            >
                <Undo class="h-4 w-4" />
            </button>

            <!-- Redo -->
            <button
                type="button"
                class="rounded-md p-2 text-gray-600 hover:bg-gray-100"
                @click="editor?.chain().focus().redo().run()"
            >
                <Redo class="h-4 w-4" />
            </button>
        </div>

        <!-- Content -->
        <EditorContent
            :editor="editor"
            :style="{ minHeight }"
            class="text-sm"
        />
    </div>
</template>

<style>
.ProseMirror {
    min-height: 180px;
}

.ProseMirror:focus {
    outline: none;
}

.ProseMirror img {
    border-radius: 0.5rem;
    margin: 1rem 0;
    max-width: 100%;
}

.ProseMirror blockquote {
    border-left: 4px solid #d1d5db;
    padding-left: 1rem;
    color: #6b7280;
}

.ProseMirror p.is-editor-empty:first-child::before {
    color: #9ca3af;
    content: attr(data-placeholder);
    float: left;
    height: 0;
    pointer-events: none;
}
</style>
