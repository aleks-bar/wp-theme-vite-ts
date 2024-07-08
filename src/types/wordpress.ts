type ImageSizes = {
    thumbnail: string,
    'thumbnail-height': number,
    'thumbnail-width': number,
    medium: string,
    'medium-height': number,
    'medium-width': number,
    medium_large: string,
    'medium_large-height': number,
    'medium_large-width': number,
    large: string,
    'large-width': number,
    'large-height': number
    '1536x1536': string,
    '1536x1536-height': number,
    '1536x1536-width': number,
    '2048x2048': string,
    '2048x2048-height': number,
    '2048x2048-width': number,
}

export interface WP_IMG {
    ID: number,
    id: number,
    title: string,
    filename: string,
    filesize: number,
    url: string,
    link: string,
    alt: string,
    author: string,
    description: string,
    caption: string,
    name: string,
    status: string,
    uploaded_to: number,
    date: string,
    modified: string,
    menu_order: number,
    mime_type: string,
    type: string,
    subtype: string,
    icon: string,
    width: number,
    height: number,
    sizes: ImageSizes,
}

export interface WP_File {
    ID: number
    alt: string
    author: string
    caption: string
    date: string
    description: string
    filename: string
    filesize: number
    height: number
    icon: string
    id: number
    link: string
    menu_order: number
    mime_type: string
    modified: string
    name: string
    status: string
    subtype: string
    title: string
    type: string
    uploaded_to: number
    url: string
    width: number
}

export interface WP_LINK {
    target: string
    title: string
    url: string
}

export interface WP_Query {
    posts_per_page?: number,
    post_type?: string
    post__in?: number[]
    paged?: number
}

export interface WP_Nav_item {
    ID: number
    attr_title: string
    classes: string[]
    comment_count: string
    comment_status: string
    db_id: number
    description: string
    filter: string
    guid: string
    menu_item_parent: string
    menu_order: number
    object: string
    object_id: string
    ping_status: string
    pinged: string
    post_author: string
    post_content: string
    post_content_filtered: string
    post_date: string
    post_date_gmt: string
    post_excerpt: string
    post_mime_type: string
    post_modified: string
    post_modified_gmt: string
    post_name: string
    post_parent: number
    post_password: string
    post_status: string
    post_title: string
    post_type: string
    target: string
    title: string
    to_ping: string
    type: string
    type_label: string
    url: string
    xfn: string
}
