import { Routes, RouterModule } from '@angular/router';
import { LoginComponent } from '../login/login.component';
import { AuthGuard } from '../_guard/auth.guard';
import { HomeComponent } from '../home/home.component';
import { UserComponent } from '../user/user.component';
import { FrameComponent } from './frame.component';
import { RepositoryComponent } from '../repository/repository.component';
import { PublishTokensComponent } from '../publish-tokens/publish-tokens.component';
import { UploadPackageComponent } from '../upload-package/upload-package.component';
import { PackagesComponent } from '../packages/packages.component';
import {FileUploadModule} from 'primeng/primeng';


export const routes: Routes = [
  { path: 'admin',
   canActivate: [AuthGuard] ,
   component: FrameComponent,
  children: [
    { path: '',  pathMatch: 'full', component: HomeComponent},
    { path: 'home',  pathMatch: 'full', component: HomeComponent},
    { path: 'user', component: UserComponent },
    { path: 'repository', component: RepositoryComponent },
    { path: 'publishtokens', component: PublishTokensComponent },
    { path: 'upload', component: UploadPackageComponent },
    { path: 'packages', component: PackagesComponent }
  ]
}
];


 export const adminRouting = RouterModule.forChild(routes);
