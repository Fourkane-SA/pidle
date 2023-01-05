export class Level {
    id: number
    title: string
    description: string
    userId: number
    size: number
    pattern: string
    finished: boolean
    published: boolean
    updated_at: string

    constructor(json: any) {
        this.id = json.id
        this.title = json.title
        this.description = json.description
        this.userId = json.userId
        this.size = json.size
        this.pattern = json.pattern
        this.finished = json.finished
        this.published = json.published
        this.updated_at = json.updated_at
    }
}
