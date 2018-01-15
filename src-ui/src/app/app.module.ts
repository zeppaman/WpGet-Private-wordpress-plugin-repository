import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';

import { AppComponent } from './app.component';

import {NoopAnimationsModule} from '@angular/platform-browser/animations';

import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { LoginComponent } from './login/login.component';
import { RouterModule, Routes } from '@angular/router';
import { FrameComponent } from './frame/frame.component';
import { NotFoundComponent } from './not-found/not-found.component';
import { HomeComponent } from './home/home.component';
import { UserComponent } from './user/user.component';
import { AuthenticationService } from './_service/authentication.services';
import { HttpModule } from '@angular/http';
import { AlertService } from './_service/alert.service';
import { FormsModule } from '@angular/forms';
import { adminRouting } from './frame/frame-routing.module';
import { appRouting } from './app-routing.module';
import { AuthGuard } from './_guard/auth.guard';

import {ToolbarModule} from 'primeng/primeng';
import {SidebarModule} from 'primeng/primeng';
import {SplitButtonModule} from 'primeng/primeng';
import {MenuModule} from 'primeng/primeng';
import { UserService } from './_service/user.service';
import { DataTableModule } from 'primeng/components/datatable/datatable';
import { SharedModule } from 'primeng/components/common/shared';
import { DialogModule } from 'primeng/components/dialog/dialog';
import { PasswordModule } from 'primeng/components/password/password';
import { MessagesModule } from 'primeng/components/messages/messages';
import { MessageModule } from 'primeng/components/message/message';
import { MessageService } from 'primeng/components/common/messageservice';
import { GrowlModule } from 'primeng/components/growl/growl';
import { BreadcrumbModule } from 'primeng/components/breadcrumb/breadcrumb';
import { RepositoryComponent } from './repository/repository.component';
import { PublishTokensComponent } from './publish-tokens/publish-tokens.component';
import { RepositoryService } from './_service/repository.service';
import { PublishTokenService } from './_service/publishtoken.service';
import {InputTextareaModule} from 'primeng/primeng';
import { DropdownModule } from 'primeng/components/dropdown/dropdown';
import { UploadPackageComponent } from './upload-package/upload-package.component';
import { PackagesComponent } from './packages/packages.component';
import {PanelModule} from 'primeng/primeng';
import {FileUploadModule} from 'primeng/primeng';

@NgModule({
  declarations: [
    AppComponent,
    LoginComponent,
    FrameComponent,
    NotFoundComponent,
    HomeComponent,
    UserComponent,
    RepositoryComponent,
    PublishTokensComponent,
    UploadPackageComponent,
    PackagesComponent
  ],
  imports: [
    adminRouting,
    appRouting,
    BrowserModule,
    BrowserAnimationsModule,
    HttpModule,
    FormsModule,
    SplitButtonModule,
    MenuModule,
    SidebarModule,
    BrowserModule,
    ToolbarModule,
    BrowserAnimationsModule,
    DataTableModule,
    SharedModule,
    DialogModule,
    PasswordModule,
    MessagesModule,
    MessageModule,
    GrowlModule,
    BreadcrumbModule,
    InputTextareaModule,
    DropdownModule,
    PanelModule,
    FileUploadModule

  ],
  providers: [AuthenticationService, AlertService,  AuthGuard, UserService, MessageService, RepositoryService, PublishTokenService],
  bootstrap: [AppComponent]
})
export class AppModule { }
