export enum UserRole {
    ADMIN = 'admin',
    STAFF = 'staff',
    CUSTOMER = 'customer'
  }
  
  export const ADMIN_ROLES = [UserRole.ADMIN];
  export const STAFF_ROLES = [UserRole.ADMIN, UserRole.STAFF];