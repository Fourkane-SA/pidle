export class User {
    id: number
    login: string
    description: string
    birth: string
    idAvatar: number
    password?: string
    email: string
    firstname: string
    lastname: string
    created?: string
    created_at: string


    constructor(json: any = {}) {
        this.id = json.id
        this.login = json.login
        this.description = json.description
        this.birth = json.birth
        this.idAvatar = json.idAvatar
        this.email = json.email
        this.firstname = json.firstname
        this.lastname = json.lastname
        this.created = json.created
        this.created_at = json.created_at
    }

}
