export class Game {
    id: number
    levelId: number
    userId: number
    completed: boolean
    created_at: string
    updated_at: string

    constructor(json: any) {
        this.id = json.id
        this.levelId = json.levelId
        this.userId = json.userId
        this.completed = json.completed
        this.created_at = json.created_at
        this.updated_at = json.updated_at
    }
}
