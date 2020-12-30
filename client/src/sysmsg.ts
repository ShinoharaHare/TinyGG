import Vue from 'vue'

export const Bus = new Vue()

// const queue: IMessageArgs[] = []

export interface IMessageOptions {
    color?: string;
    timeout?: number;
    multiline?: boolean;
}

export interface IMessageData extends Required<IMessageOptions> {
    message: string;
}

const Default: IMessageOptions = {
    color: '',
    timeout: 2000
}

export function sendMessage(message: string, opts?: IMessageOptions) {
    Bus.$emit('message', { ...Default, message, ...opts })
}
